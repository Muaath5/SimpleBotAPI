<?php
namespace SimpleBotAPI;

use SimpleBotAPI\BotSettings;
use SimpleBotAPI\UpdatesHandler;

use SimpleBotAPI\Exceptions\TelegramException;
use SimpleBotAPI\Exceptions\TelegramChatMigratedException;
use SimpleBotAPI\Exceptions\TelegramFloodException;
use SimpleBotAPI\Exceptions\TelegramUnauthorizedException;

/**
 * Telegram Bot Client
 * @version Bot API 5.3
 */
class TelegramBot
{
    public const TIMEOUT = 63;

    private string $Token;

    public string $HashingMethod = 'sha512';

    public BotSettings $Settings;
    
    private ?UpdatesHandler $UpdatesHandler = null;
    private \CurlHandle $curl;

    public function __construct(string $token, UpdatesHandler $updatesHandler = null, BotSettings $settings = null)
    {
        # Check if token is match Regex format
        if (preg_match('/^(\d+):[\w-]{30,}$/', $token, $matches) === 0)
        {
            throw new TelegramUnauthorizedException('Invalid Bot Token');
        }

        $this->Token = $token;
        
        $this->Settings = $settings ?? new BotSettings();   

        if (!empty($updatesHandler))
        {
            $this->UpdatesHandler = $updatesHandler;
            $this->UpdatesHandler->SetBot($this);
        }
        

        # Initializing cURL Requests

        # If failed to create cURL
        if (($this->curl = curl_init()) == false)
        {
            throw new \RuntimeException("Creating cURL handle failed");
        }
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

    public function SetUpdatesHandler(UpdatesHandler $newUpdatesHandler)
    {
        $UpdatesHandler = $newUpdatesHandler;
        $UpdatesHandler->SetBot($this);
    }

    protected function OnUpdate(\stdClass $update) : bool
    {
        if (empty($this->UpdatesHandler))
        {
            throw new \InvalidArgumentException("This bot doesn't has updates handler");
        }
        
        $this->Settings->LastUpdateID = $update->update_id;
        $this->Settings->LastUpdateDate = time();

        switch ($update)
        {
            case property_exists($update, 'message'):
                return $this->UpdatesHandler->MessageHandler($update->message);
            
            case property_exists($update, 'edited_message'):
                return $this->UpdatesHandler->EditedMessageHandler($update->edited_message);


            case property_exists($update, 'channel_post'):
                return $this->UpdatesHandler->ChannelPostHandler($update->channel_post);

            case property_exists($update, 'edited_channel_post'):
                return $this->UpdatesHandler->EditedChannelPostHandler($update->edited_channel_post);


            case property_exists($update, 'inline_query'):
                return $this->UpdatesHandler->InlineQueryHandler($update->inline_query);

            case property_exists($update, 'chosen_inline_query'):
                return $this->UpdatesHandler->ChosenInlineQueryHandler($update->chosen_inline_query);


            case property_exists($update, 'callback_query'):
                return $this->UpdatesHandler->CallbackQueryHandler($update->callback_query);


            case property_exists($update, 'my_chat_member'):
                return $this->UpdatesHandler->MyChatMemberHandler($update->my_chat_member);

            case property_exists($update, 'chat_member'):
                return $this->UpdatesHandler->ChatMemberHandler($update->chat_member);


            case property_exists($update, 'shipping_query'):
                return $this->UpdatesHandler->ShippingQueryHandler($update->shipping_query);

            case property_exists($update, 'pre_checkout_query'):
                return $this->UpdatesHandler->PreCheckoutQueryHandler($update->pre_checkout_query);

            default:
                # This means Library version is out-dated, Or it's a faked update
                return false;
        }
    }

    public function SaveSettings()
    {
        $this->Settings->Export();
    }

    public function OnWebhookUpdate() : bool
    {
        $Update = json_decode(file_get_contents('php://input'));
        if (empty($Update)) return false;

        if ($this->Settings->AutoHandleDuplicateUpdates)
        {
            # If sooner than 2 weeks
            if ($this->Settings->LastUpdateDate < strtotime('-2 week'))
            {
                if ($this->Settings->LastUpdateID >= $Update->update_id)
                {
                    // This update is fake or duplicate by ID
                    return false;
                }
            }
            $this->Settings->LastUpdateID = $Update->update_id;
        }

        # Check `token_hash`

        if ($this->Settings->CheckUpdates)
        {
            if ($_GET['token_hash'] != hash($this->HashingMethod, $this->Token))
            {
                # Fake update
                return false;
            }
        }

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

        foreach ($updates as $update)
        {
            if ($update->update_id > $this->Settings->LastUpdateID)
            {
                $this->OnUpdate($update);
            }
            $this->Settings->LastUpdateID = max($this->Settings->LastUpdateID, $update->update_id);
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
        file_put_contents($save_path, file_get_contents("https://{$this->Settings->APIHost}/bot{$this->Token}/file{$file->file_path}"));
    }

    /**
     * Use this method to send method via the HTTP Response, Only can be used in webhooks
     * @param string $method
     */
    public function WebhookResponse(string $method, array $params = []) : void
    {
        $params['method'] = $method;

        $payload = json_encode($params);
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($payload));
        echo $payload;
    }

    public function SetBotWebhook(string $host, int $max_connections = 40, bool $auth = true)
    {
        $this->Settings->CheckUpdates = $auth;
        return $this->SetWebhook([
            'url' => $host . ($auth ? '?token_hash=' . hash($this->HashingMethod, $this->Token) : ''),
            'max_connections' => $max_connections
        ]);
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
            # If the error contains additional info
            if (property_exists($object, 'parameters'))
            {
                # Flood error
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

                # Chat ID migrated error
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
            else if ($object->error_code == 401)
            {
                throw new TelegramUnauthorizedException($object->description);
            }
            else
            {
                throw new TelegramException($object);
            }
        }
        return $object->result;
    }
}
