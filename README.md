# Simple Bot API
[![Licence: GPL v3.0](https://img.shields.io/badge/Licence-GPL%20v3.0-green)](LICENCE)
[![Bot API Version: 5.3](https://img.shields.io/badge/Bot%20API%20Version-5.3-dodgerblue)](https://core.telegram.org/bots/api#april-26-2021)
[![PHP Unit Tests](https://github.com/Muaath5/SimpleBotAPI/actions/workflows/php.yml/badge.svg)](https://github.com/Muaath5/SimpleBotAPI/actions/workflows/php.yml)


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
$Bot = new TelegramBot('bot_token', new BotSettings(new MyUpdatesHandler()));
```

### Bot Settings
I've added `BotSettings` for two reasons:
1. Add ability to auto-handle for exceptions
2. Save your bot from duplicate, And not needed updates

Bot Settings feature:
- Auto handle flood exception
- Auto handle chat-migrated exception
- Auto handle for duplicate updates
- Auto handle for settings.
- Allowing only some updates.
- Add Updates timeout if you're using long-polling
- Add your Bot Updates Handler class.

#### Importing Bot Settings
```php
$Settings = BotSettings::Import('/home/main/bots/MyBot/MyBotSettings.json');
```

#### Exporting Bot Settings
```php
$Settings->Export('/home/main/bots/MyBot/MyBotSettings.json');
```
Or you can **not specify** path, So it'll be created in the folder your UpdatesHandler was there, And with name: `{YourUpdatesHandlerName}Settings.json`
```php
$Settings->Export();
```

#### Auto manage for Bot Settings Export
```php
$Settings->AutoManageSettings = true;
```


### Receiving updates
#### Webhook
```php
$success = $Bot->OnWebhookUpdate(file_get_contents('php://input'));
```
`$success` will be boolean, So you can log or add to your statistics count of fails for the bot.

#### Long-polling (getUpdates)
```php
while (time() < strtotime('+10 minutes'))
{
    $Bot->ReceiveUpdates();
}
```

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
- FAQ Bot, Which has one message & buttons to swap between questions, And you can see answers. Also you can search any question in Inline mode.

All of these examples doesn't need any databases or files to storage, Only Settings file.

## Todo
- Add ability to create UpdatesHandler form settings with taking care of constructor

## License
GPL 3.0 only