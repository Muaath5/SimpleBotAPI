<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/bot-api/BotAPIExceptions.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/bot-api/UpdatesHandler.php';

/**
 * Telegram bot client
 * @version Bot API 5.3
 */
class TelegramBot
{
    public const TIMEOUT = 63;

    private string $Token = "";
    private string $TelegramBotUrl = "";
    private string $TelegramBotFileUrl = "";
    
    private $curl;
    private ?UpdatesHandler $UpdatesHandler = null;

    private ?int $LastUpdateID = null;

    public function __construct(string $token, UpdatesHandler $updates_handler = null, string $api_host = 'https://api.telegram.org')
    {
        if (preg_match('/^(\d+):[\w-]{30,}$/', $token, $matches) === 0) {
            throw new InvalidArgumentException('The supplied token does not look correct...');
        }

        $this->Token = $token;
        $this->TelegramBotUrl = "{$api_host}/bot{$this->Token}";
        $this->TelegramBotFileUrl = "{$api_host}/file/bot{$this->Token}";
        $this->UpdatesHandler = $updates_handler;

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this::TIMEOUT); // botAPI might take 60s before returning error
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    public function SetUpdatesHandler(UpdatesHandler $new_updates_handler)
    {
        $this->UpdatesHandler = $new_updates_handler;
    }

    public function OnUpdate(object $update) : bool
    {
        $this->LastUpdateID = $update->update_id;
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
                # Don't do anything, Only when Bot API version in later
                return false;
        }
    }

    public function ReceiveUpdates(int $timeout = TelegramBot::TIMEOUT)
    {
        $updates = $this->GetUpdates();
        foreach ($updates as $update)
        {
            $this->OnUpdate($update);
        }
    }

    /**
     * [WIP] Support downloading files
     * @param int $file_id File ID for the bot
     * @param string $save_path Path to save the downloaded file
     */
    public function DownloadFile(int $file_id, string $save_path)
    {
        $file = $this->GetFile($file_id);
        file_put_contents($save_path, file_get_contents("{$this->BotAPIFileUrl}/{$file->file_path}"));
    }

    /**
     * Template function to make API calls using method name and array of parameters
     *
     * @param string $method The method name from https://core.telegram.org/bots/api
     * @param array $params The arguments of the method, as an array
     * @return stdClass|bool
     * @throws TelegramException, RuntimeException
     */
    public function __call(string $method, array $params) {
        curl_setopt($this->curl, CURLOPT_URL, "{$this->TelegramBotUrl}/$method");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params[0] ?? []);

        $result = curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            throw new RuntimeException(curl_error($this->curl), curl_errno($this->curl));
        }
        $object = json_decode($result);
        if (!$object->ok) {
            if (property_exists($object, 'parameters')) {
                if (property_exists($object->parameters, 'retry_after')) {
                    throw new TelegramFloodWait($object);
                }
                if (property_exists($object->parameters, 'migrate_to_chat_id')) {
                    throw new TelegramChatMigrated($object);
                }
            }
            throw new TelegramException($object);
        }
        return $object->result;
    }
}
