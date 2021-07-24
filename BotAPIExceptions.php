<?php
/**
 * Some of Bot API Errors
 * @version Bot API 5.3
 */

class TelegramException extends Exception
{
    protected $result;

    public function __construct(stdClass $result) {
        $this->result = $result;
        parent::__construct($result->description, $result->error_code);
    }

    public function __toString(): string {
        return get_class($this) . ": <code>{$this->code} ({$this->message})</code>\n<b>Trace:</b>\n{$this->getTraceAsString()}";
    }

    public function getResult(): stdClass {
        return $this->result;
    }
}

class TelegramFloodWait extends TelegramException
{
    public int $retry_after;

    public function __construct(stdClass $result) {
        $this->retry_after = $result->parameters->retry_after;
        parent::__construct($result);
    }
}

class TelegramChatMigrated extends TelegramException
{
    public $migrate_to_chat_id;

    public function __construct(stdClass $result) {
        $this->migrate_to_chat_id = $result->parameters->migrate_to_chat_id;
        parent::__construct($result);
    }
}