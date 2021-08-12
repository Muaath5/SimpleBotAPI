<?php

namespace SimpleBotAPI;

use SimpleBotAPI\TelegramException;

class TelegramFloodException extends TelegramException
{
    public int $retry_after;

    public function __construct(\stdClass $result) {
        $this->retry_after = $result->parameters->retry_after;
        parent::__construct($result);
    }
}