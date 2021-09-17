<?php
namespace SimpleBotAPI;

use InvalidArgumentException;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\UpdatesHandler;

use SimpleBotAPI\TelegramException;
use SimpleBotAPI\TelegramChatMigrated;
use SimpleBotAPI\TelegramFloodWait;

/**
 * Telegram Bot Client
 * @version Bot API 5.3
 */
class TelegramBot
{
    public const TIMEOUT = 63;

    private string $Token;

    public BotSettings $Settings;
    
    private $curl;
    private ?UpdatesHandler $UpdatesHandler = null;

    public function __construct(string $token, UpdatesHandler $updatesHandler, BotSettings $settings = null)
    {
        # Check if token is match Regex format
        if (preg_match('/^(\d+):[\w-]{30,}$/', $token, $matches) === 0)
        {
            throw new TelegramUnauthorizedException('Invalid Bot Token');
        }

        $this->Token = $token;
        
        $this->UpdatesHandler = $updatesHandler;
        $this->Settings = $settings ?? new BotSettings();

        $this->UpdatesHandler->SetBot($this);

        # Initializing cURL Requests
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this::TIMEOUT); // Bot API might take 60s before returning error
    }

    public function __destruct()
    {
        if ($this->Settings->AutoHandleSettings)
        {
            $this->SaveSettings();
        }
        curl_close($this->curl);
    }

    public function SetUpdatesHandler(BotSettings $new_settings)
    {
        $this->Settings = $new_settings;
    }

    protected function OnUpdate(\stdClass $update) : bool
    {
        if (empty($this->Settings->UpdatesHandler))
        {
            throw new InvalidArgumentException("This bot can't handle updates");
        }

        if ($this->Settings->AutoHandleDuplicateUpdates)
        {
            $this->Settings->LastUpdateID = $update->update_id;
            $this->Settings->LastUpdateDate = time();
        }

        switch ($update)
        {
            case property_exists($update, 'message'):
                return $this->Settings->UpdatesHandler->MessageHandler($update->message);
            
            case property_exists($update, 'edited_message'):
                return $this->Settings->UpdatesHandler->EditedMessageHandler($update->edited_message);


            case property_exists($update, 'channel_post'):
                return $this->Settings->UpdatesHandler->ChannelPostHandler($update->channel_post);

            case property_exists($update, 'edited_channel_post'):
                return $this->Settings->UpdatesHandler->EditedChannelPostHandler($update->edited_channel_post);


            case property_exists($update, 'inline_query'):
                return $this->Settings->UpdatesHandler->InlineQueryHandler($update->inline_query);

            case property_exists($update, 'chosen_inline_query'):
                return $this->Settings->UpdatesHandler->ChosenInlineQueryHandler($update->chosen_inline_query);


            case property_exists($update, 'callback_query'):
                return $this->Settings->UpdatesHandler->CallbackQueryHandler($update->callback_query);


            case property_exists($update, 'my_chat_member'):
                return $this->Settings->UpdatesHandler->MyChatMemberHandler($update->my_chat_member);

            case property_exists($update, 'chat_member'):
                return $this->Settings->UpdatesHandler->ChatMemberHandler($update->chat_member);


            case property_exists($update, 'shipping_query'):
                return $this->Settings->UpdatesHandler->ShippingQueryHandler($update->shipping_query);

            case property_exists($update, 'pre_checkout_query'):
                return $this->Settings->UpdatesHandler->PreCheckoutQueryHandler($update->pre_checkout_query);

            default:
                # Don't do anything, Only when Bot API version in later
                return false;
        }
    }

    public function SaveSettings()
    {
        $this->Settings->Export();
    }

    public function OnWebhookUpdate(string $json_update) : bool
    {
        $Update = json_decode($json_update);
        if (empty($Update)) return false;
        return $this->OnUpdate($Update);
    }

    public function ReceiveUpdates(int $limit = 100, int $offset = 0) : bool
    {
        if ($this->Settings->AutoHandleDuplicateUpdates)
        {
            # If sooner than 2 weeks
            if ($this->Settings->LastUpdateDate < strtotime('-2 week'))
            {
                $offset = $this->Settings->LastUpdateID + 1;
            }
        }

        $updates = $this->GetUpdates([
            'limit' => $limit,
            'offset' => $offset,
            'timeout' => $this->Settings->UpdatesTimeout,
            'allowed_updates' => json_encode($this->Settings->AllowedUpdates)
        ]);
        if (count($updates) > 0)
        {
            foreach ($updates as $update)
            {
                if ($update->update_id > $this->Settings->LastUpdateID)
                {
                    $this->OnUpdate($update);
                }
            }
            
            if ($this->Settings->AutoHandleDuplicateUpdates && count($updates))
            {
                $this->Settings->LastUpdateID = $updates[count($updates)-1]->update_id;
            }
        }
        return true;
    }

    /**
     * Download file on the server.
     * @param string $file_id File ID for the bot
     * @param string $save_path Absolute path to save the downloaded file
     */
    public function DownloadFile(string $file_id, string $save_path)
    {
        $file = $this->GetFile($file_id);
        file_put_contents($save_path, file_get_contents("{$this->TelegramBotFileUrl}/{$file->file_path}"));
    }

    public function WebhookResponse(string $method, array $params = []) : void
    {
        $params['method'] = $method;

        $payload = json_encode($params);
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($payload));
        echo $payload;
    }

    /**
     * Template function to make API calls using method name and array of parameters
     *
     * @param string $method The method name from https://core.telegram.org/bots/api
     * @param array $params The arguments of the method, as an array
     * @return stdClass|bool|string|int|float
     * @throws TelegramException,\RuntimeException
     */
    public function __call(string $method, array $params) : mixed
    {
        curl_setopt($this->curl, CURLOPT_URL, "{$this->Settings->APIHost}/bot{$this->Token}/$method");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params[0] ?? []);

        $result = curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            throw new \RuntimeException(curl_error($this->curl), curl_errno($this->curl));
        }
        $object = json_decode($result);
        if (!$object->ok)
        {
            if (property_exists($object, 'parameters'))
            {
                if (property_exists($object->parameters, 'retry_after'))
                {
                    if ($this->Settings->AutoHandleFloodException)
                    {
                        usleep($object->parameters->retry_after * 1000000 + 1000000);
                        # Recall same method
                        $this->$method($params[0] ?? []);
                    }
                    else
                    {
                        throw new TelegramFloodException($object);
                    }
                }
                if (property_exists($object->parameters, 'migrate_to_chat_id'))
                {
                    if ($this->Settings->AutoHandleChatMigratedException)
                    {
                        $params[0]['chat_id'] = $object->parameters->migrate_to_chat_id;
                        $this->$method($params[0]);
                    }
                    else
                    {
                        throw new TelegramChatMigratedException($object);
                    }
                }
            }
            else
            {
                throw new TelegramException($object);
            }
        }
        return $object->result;
    }
}
