# Simple Bot API
[![Licence: GPL v3.0](https://img.shields.io/badge/Licence-GPL%20v3.0-green)](LICENCE)
[![Bot API Version: 5.3](https://img.shields.io/badge/Bot%20API%20Version-5.3-dodgerblue)](https://core.telegram.org/bots/api#april-26-2021)
![Workflow status](https://github.com/Muaath5/MuaathBots/actions/workflows/PHP%20Unit%20Tests/badge.svg)


This is simple Telegram Bot API in PHP, Supports Bot API 5.3.

This Library should support all Bot API versions in methods.

## Features
- Supports all Bot API methods without needing to update.
- Has updates handler which makes handling updates easier
- Based on cURL.
- Downloadable via composer.
- Has tests which ensure that everything work.
- GitHub Actions.
- Has examples of some made bots.
- Full Documented.
- Has exceptions that make it easy to handle it.
- Up to date.

## Installation

### Prerequists
- PHP 8.0 Or Higher
- cURL PHP extension enabled

### Installing
Install it via composer:
```sh
composer require muaath5/simple-bot-api
```
Or Download `src/` folder and require it in your code.

## Usage

### Using method
It should be in this format:
```
$Result = $Bot->{methodName}([
    '{paramName}' => {value1},
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

### Creating bot
```php
$Bot = new TelegramBot('bot_token', new MyUpdatesHandler());
```

### Receiving webhook updates
```php
$success = $Bot->OnWebhookUpdate(file_get_contents('php://input'));
```
`$success` will be boolean, So you can log or add to your statistics count of fails for the bot.


## Contibuting
You can contribute by:
- Report a bug.
- Suggest a feature.
- Improve code via Pull request.
- Adding more examples to `examples/` via Pull request.
- Adding documentation for the library.
- Updating the library to the next version of Bot API.
- Adding more tests in `test/` folder

## Contact me
- Telegram: [@Muaath_5](https://t.me/Muaath_5) [Suggested]
- Email: muaath1428@hotmail.com

## Examples
Currently, There're three examples in `examples/` folder:
- Echo Bot, Which sends same as you sent
- Weclome Bot, Which sends Welcome & ID of user who joins the group
- Contact Me Bot, Which is a Bot be same as you, You'll receive messages & Reply

All of these examples doesn't need any databases or files to storage.

## License
GPL 3.0 only