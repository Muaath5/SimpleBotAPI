<?php

namespace SimpleBotAPI\Exceptions;

/**
 * Telegram Bot API Exception
 * @version Bot API 5.3
 */
class TelegramException extends \Exception
{
    protected $result;

    public function __construct(\stdClass $result) {
        $this->result = $result;
        parent::__construct($result->description, $result->error_code);
    }

    public function __toString(): string {
        return get_class($this) . ": <code>{$this->code} ({$this->message})</code>\n<b>Trace:</b>\n{$this->getTraceAsString()}";
    }

    public function getResult(): \stdClass {
        return $this->result;
    }
}