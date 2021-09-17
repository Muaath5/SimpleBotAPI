<?php

namespace SimpleBotAPI;

use SimpleBotAPI\TelegramException;

class TelegramUnauthorizedException extends TelegramException
{
    public function __construct(\stdClass|string $error, int $error_code = 401)
    {
        if (is_string($error))
        {
            $this->code = $error_code;
            $this->message = $error;
        }
        else
        {
            $this->code = $error->error_code;
            $this->message = $error->description;
        }

    }
}