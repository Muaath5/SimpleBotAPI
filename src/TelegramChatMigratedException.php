<?php
namespace SimpleBotAPI;

use SimpleBotAPI\TelegramException;

class TelegramChatMigratedException extends TelegramException
{
    public $migrate_to_chat_id;

    public function __construct(\stdClass $result) {
        $this->migrate_to_chat_id = $result->parameters->migrate_to_chat_id;
        parent::__construct($result);
    }
}