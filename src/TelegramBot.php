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

    public function __construct(string $token, UpdatesHandler $updatesHandler = null, BotSettings $settings = null, bool $logErrors = true)
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
        
        if (empty($update))
        {
            error_log("Update is empty");
            http_response_code(400);
            return false;
        }


        # Checking the update
        if ($this->Settings->HandleDuplicateUpdatesLevel)
        {
            # Sooner than one week, Check the ID
            if ($this->Settings->LastUpdateDate > strtotime('-1 week'))
            {
                $isRealUpdate = false;

                if ($this->Settings->HandleDuplicateUpdatesLevel == 1 && $this->Settings->LastUpdateID < $update->update_id)
                    $isRealUpdate = true;
                else if ($this->Settings->HandleDuplicateUpdatesLevel == 3 && $this->Settings->LastUpdateID === $update->update_id - 1)
                    $isRealUpdate = true;

                # Check if update is in the write order
                if ($isRealUpdate || $this->Settings->LastUpdateID == -1)
                {
                    $this->Settings->LastUpdateID = $update->update_id;
                    $this->Settings->LastUpdateDate = time();
                }
                else
                {
                    # Error, Update ID wrong
                    error_log("Last Update ID ({$this->Settings->LastUpdateID}) != Update ID ({$update->update_id}) - 1");
                    if ($this->Settings->HandleDuplicateUpdatesLevel == 2 || $this->Settings->HandleDuplicateUpdatesLevel == 4)
                        http_response_code(400);
                    return false;
                }
            }
            # Otherwise, Don't check the ID, Take it as it is
            else 
            {
                $this->Settings->LastUpdateID = max($this->Settings->LastUpdateID, $update->update_id);
                $this->Settings->LastUpdateDate = time();
            }
        }

        # Calling update handler
        switch ($update)
        {
            case property_exists($update, 'message') && array_search('message', $this->Settings->AllowedUpdates) !== false:
                if ($this->Settings->AutoSaveBotUsers && !array_search($update->message->from->id, $this->Settings->BotUsers))
                {
                    array_push($this->Settings->BotUsers, $update->message->from->id);
                }
                return $this->UpdatesHandler->MessageHandler($update->message);
            
            case property_exists($update, 'edited_message') && (array_search('edited_message', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->EditedMessageHandler($update->edited_message);


            case property_exists($update, 'channel_post') && (array_search('channel_post', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->ChannelPostHandler($update->channel_post);

            case property_exists($update, 'edited_channel_post') && (array_search('edited_channel_post', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->EditedChannelPostHandler($update->edited_channel_post);


            case property_exists($update, 'inline_query') && (array_search('inline_query', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->InlineQueryHandler($update->inline_query);

            case property_exists($update, 'chosen_inline_query') && (array_search('chosen_inline_query', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->ChosenInlineQueryHandler($update->chosen_inline_query);


            case property_exists($update, 'callback_query') && (array_search('callback_query', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->CallbackQueryHandler($update->callback_query);


            case property_exists($update, 'my_chat_member') && (array_search('my_chat_member', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->MyChatMemberHandler($update->my_chat_member);

            case property_exists($update, 'chat_member') && (array_search('chat_member', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->ChatMemberHandler($update->chat_member);
                
            case property_exists($update, 'chat_join_request') && (array_search('chat_join_request', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->ChatJoinRequestHandler($update->chat_join_request);


            case property_exists($update, 'shipping_query') && (array_search('shipping_query', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->ShippingQueryHandler($update->shipping_query);

            case property_exists($update, 'pre_checkout_query') && (array_search('pre_checkout_query', $this->Settings->AllowedUpdates) !== false):
                return $this->UpdatesHandler->PreCheckoutQueryHandler($update->pre_checkout_query);

            default:
                # This means Library version is out-dated, Or it's a faked update
                error_log("ERROR: Update type is not allowed or unknown!");
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

        # Check `token_hash`
        if ($this->Settings->CheckUpdates)
        {
            if ($_GET['token_hash'] != hash($this->HashingMethod, $this->Token))
            {
                # Fake update
                error_log("ERROR[401]: Received invalid token_hash={$_GET['token_hash']}");
                http_response_code(401);
                return false;
            }
        }

        return $this->OnUpdate($Update);
    }

    public function ReceiveUpdates(int $limit = 100, int $offset = 0) : bool
    {
        if ($this->Settings->HandleDuplicateUpdatesLevel)
        {
            # If sooner than 2 weeks
            if ($this->Settings->LastUpdateDate >= strtotime('-1 week'))
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
            $this->OnUpdate($update);
            $this->Settings->LastUpdateID = max($this->Settings->LastUpdateID, $update->update_id);
        }
        return true;
    }

    # A function to send messages to all bot users
    public function BroudcastMessage(string $text, array $reply_markup = [], string $parse_mode = 'HTML', bool $disable_notification = false)
    {
        $usersCnt = count($this->Settings->BotUsers);
        error_log("INFO: Broadcasting to {$usersCnt} users");
        foreach ($this->Settings->BotUsers as $userId)
        {
            $this->SendMessage([
                'chat_id' => $userId,
                'text' => $text,
                'parse_mode' => $parse_mode,
                'reply_markup' => json_encode($reply_markup),
                'disable_notification' => $disable_notification
            ]);
        }
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

        error_log("INFO: Webhook responsed method {$method}");
        http_response_code(200);

        echo $payload;
    }

    public function SetBotWebhook(string $host, int $max_connections = 40, bool $auth = true)
    {
        $newWebhookUrl = $host . ($auth ? '?token_hash=' . hash($this->HashingMethod, $this->Token) : '');
        error_log("INFO: Webhook was set to {$newWebhookUrl}");
        $this->Settings->CheckUpdates = $auth;
        return $this->SetWebhook([
            'url' => $newWebhookUrl,
            'max_connections' => $max_connections,
            'allowed_updates' => json_encode($this->Settings->AllowedUpdates)
        ]);
    }

    /**
     * Template function to make API calls using method name and array of parameters
     *
     * @param string $method The method name from https://core.telegram.org/bots/api
     * @param array $params The arguments of the method, as an array
     * @return stdClass|bool|string|int|float The return from the method after json_decode
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
                    $secondsToWait = $object->parameters->retry_after * 1000000;
                    error_log("WARNING: Flood error, Retry after {$secondsToWait}");
                    if ($this->Settings->AutoHandleFloodException)
                    {
                        usleep($secondsToWait + 1000000);
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
