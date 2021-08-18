<?php

namespace SimpleBotAPI;

use SimpleBotAPI\UpdatesHandler;

use function PHPUnit\Framework\throwException;

class BotSettings
{
    public ?UpdatesHandler $UpdatesHandler = null;
    public string $ReceivingUpdatesType = 'webhook';
    public int $UpdatesTimeout = 1;
    public array $AllowedUpdates = ['message', 'edited_message', 'channel_post', 'edited_chanel_post', 'callback_query', 'inline_query', 'my_chat_member'];
    public int $LastUpdateID = 0;
    public int $LastUpdateDate = 0;

    public bool $AutoHandleSettings = false;
    public bool $AutoHandleDuplicateUpdates = true;
    public bool $AutoHandleFloodException = true;
    public bool $AutoHandleChatMigratedException = true;
    
    public string $APIHost = 'https://api.telegram.org';

    public string $SaveFilePath = '/';

    public function __construct(
        UpdatesHandler $updates_handler = null,
        string $save_file_path = '/',
        string $receiving_updates_type = 'webhook',
        int $updates_timeout = 1,
        array $allowed_updates = ['message', 'edited_message', 'channel_post', 'edited_chanel_post', 'callback_query', 'inline_query', 'my_chat_member'],

        bool $auto_handle_settings = false,
        bool $auto_handle_duplicate_updates = true,
        bool $auto_handle_flood = true,
        bool $auto_handle_chat_migrated = true,

        string $api_host = 'https://api.telegram.org'
    )
    {
        # Bot updates
        $this->UpdatesHandler = $updates_handler;
        strtolower($receiving_updates_type);
        $this->ReceivingUpdatesType = ($receiving_updates_type == 'wiki' || $receiving_updates_type == 'long-polling' || $receiving_updates_type == 'getupdates' ? 'long-polling' : 'webhook');
        $this->AllowedUpdates = $allowed_updates;
        $this->UpdatesTimeout = $updates_timeout;

        # Auto handle for features
        $this->AutoHandleDuplicateUpdates = $auto_handle_duplicate_updates;
        $this->AutoHandleSettings = $auto_handle_settings;
        $this->AutoHandleFloodException = $auto_handle_flood;
        $this->AutoHandleChatMigratedException = $auto_handle_chat_migrated;

        # API Host
        if (filter_var($api_host, FILTER_VALIDATE_URL) === false)
            throw new \InvalidArgumentException("API Host not found!");
        $this->APIHost = $api_host;

        if (!file_exists($save_file_path))
            throw new \InvalidArgumentException("SaveFilePath not found!");
        $this->SaveFilePath = $save_file_path;
    }

    public static function Import(string $file_path)
    {
        $json_data = json_decode(file_get_contents($file_path));
        return SELF::Recast('SimpleBotAPI\BotSettings', $json_data);
    }

    public function Export(string $save_file_path = '')
    {
        if ($save_file_path == '')
            $save_file_path = $this->SaveFilePath;

        $data = json_decode(json_encode($this));
        $data->UpdatesHandler = get_class($this->UpdatesHandler);
        file_put_contents($save_file_path, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * recast stdClass object to an object with type
     *
     * @param string $className
     * @param stdClass $object
     * @throws InvalidArgumentException
     * @return mixed new, typed object
     */
    private static function Recast(string $className, \stdClass &$object)
    {
        if (!class_exists($className))
            throw new \InvalidArgumentException(sprintf('Inexistant class %s.', $className));

        $new = new $className();

        foreach($object as $property => $value)
        {
            if ($property == 'UpdatesHandler')
            {
                if (class_exists($value))
                    $new->$property = new $value();
            }
            else
            {
                $new->$property = $value;
            }
            unset($object->$property);
        }
        unset($value);
        unset($object);
        return $new;
    }
}