# Simple Bot API
[![Licence: GPL v3.0](https://img.shields.io/badge/Licence-GPL%20v3.0-green)](LICENCE)
[![Bot API Version: 5.3](https://img.shields.io/badge/Bot%20API%20Version-5.3-dodgerblue)](https://core.telegram.org/bots/api#april-26-2021)


This is simple Telegram Bot API in PHP, Supports Bot API 5.3.

This Library should support all Bot API versions.

## Installation
Install it via composer:
```sh
composer require muaath5/simple-bot-api
```

## Using method
It should be in this format:
```
$Bot->{methodName}([
    '{paramName}' => {value},
    '{paramName2}' => {value2}
]);
```

Like this:
```php
$Bot->SendMessage([
    'chat_id' => '@Muaath_Alqarni',
    'text' => 'Some post..'
]);

$my_channel_info = $Bot->getChat([
    'chat_id' => '@Muaath_Alqarni',
]);
```

The method has no problem if it was upper or lower case.

## Creating bot
```php
$Bot = new TelegramBot('bot_token', new MyUpdatesHandler());
```

## Receiving webhook updates
```php
$Bot->OnWebhookUpdate(file_get_contents('php://input'));
```

## License
GPL 3.0 only