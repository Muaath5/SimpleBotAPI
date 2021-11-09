<?php

namespace SimpleBotAPI;

use stdClass;

class BotSettings
{
    public array $AllowedUpdates = [
        'message',
        'channel_post',
        'callback_query',
        'inline_query',
        'my_chat_member',
        'chat_join_request'
    ];

    # These should be set by library's user
    public array $BotAdmins = [];
    public ?stdClass $AdditionalData = null;

    public int $UpdatesTimeout = 1;
    public int $LastUpdateID = -1;
    public int $LastUpdateDate = 0;

    public bool $CheckUpdates = false;

    public array $BotUsers = [];
    public bool $AutoSaveBotUsers = false;

    public int $HandleDuplicateUpdatesLevel = 1;

    public bool $AutoHandleSettings = false;
    public bool $AutoHandleFloodException = true;
    public bool $AutoHandleChatMigratedException = true;
    
    public string $APIHost = 'https://api.telegram.org';

    public string $SaveFilePath = '';

    public function __construct(
        bool $auto_handle_settings = false,
        string $save_file_path = '',
        int $updates_timeout = 1,
        array $allowed_updates = ['message', 'edited_message', 'channel_post', 'edited_channel_post', 'callback_query', 'inline_query', 'my_chat_member'],

        int $handle_duplicate_updates_level = 1,
        bool $auto_handle_flood = true,
        bool $auto_handle_chat_migrated = true,

        string $api_host = 'https://api.telegram.org'
    )
    {
        // TODO in next update..
        /*
        if (!empty($save_file_path))
        {
            if (!empty($save_file_path))
            {
                $importedSettings = BotSettings::Import($save_file_path);
            }
            else
            {
                $importedSettings = new BotSettings();
            }
        }
        */

        $this->AllowedUpdates = $allowed_updates;
        $this->UpdatesTimeout = $updates_timeout;

        # Auto handle for features
        $this->HandleDuplicateUpdatesLevel = $handle_duplicate_updates_level;
        $this->AutoHandleSettings = $auto_handle_settings;
        $this->AutoHandleFloodException = $auto_handle_flood;
        $this->AutoHandleChatMigratedException = $auto_handle_chat_migrated;

        # API Host
        if (filter_var($api_host, FILTER_VALIDATE_URL) === false)
            throw new \InvalidArgumentException("API Host not found!");
        
        $this->APIHost = $api_host;
        
        $this->SaveFilePath = $save_file_path;
    }

    public static function Import(string $file_path)
    {
        $json_data = json_decode(file_get_contents($file_path));
        return SELF::Recast('SimpleBotAPI\BotSettings', $json_data);
    }

    public function Export(string $save_file_path = '')
    {
        $data = json_decode(json_encode($this));

        if (empty($save_file_path))
        {
            if (!empty($this->SaveFilePath))
                $save_file_path = $this->SaveFilePath;
            else
                throw new \InvalidArgumentException('$saved_file_path variable is empty');
        }


        $data->SaveFilePath = $save_file_path;
        file_put_contents($save_file_path, json_encode($data, JSON_PRETTY_PRINT));

        if ($data->AutoSaveBotUsers)
        {
            file_put_contents('users_db.json', json_encode($data->BotUsers, JSON_PRETTY_PRINT));
        }
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
            throw new \InvalidArgumentException("Inexistant class {$className}");

        $new = new $className();
        foreach($object as $property => $value)
        {
            $new->$property = $value;
            unset($object->$property);
        }
        unset($value);
        unset($object);
        return $new;
    }
}