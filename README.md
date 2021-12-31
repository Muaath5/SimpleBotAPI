# Simple Bot API
[![Licence: GPL v3.0](https://img.shields.io/badge/Licence-GPL%20v3.0-green)](LICENCE)
[![Bot API Version: 5.6](https://img.shields.io/badge/Bot%20API%20Version-5.6-dodgerblue)](https://core.telegram.org/bots/api)
[![PHP Unit Tests](https://github.com/Muaath5/SimpleBotAPI/actions/workflows/php.yml/badge.svg)](https://github.com/Muaath5/SimpleBotAPI/actions/workflows/php.yml)


This is a simple Telegram Bot API library in PHP.
This library should support all Bot API methods in any version update.
I created this library to make creating bots more simpliler, No need to bunch of classes And objects after today!

## Features
- Always supports all Bot API methods
- Auto handling for flood & chat ID migrating errors
- Uses `stdClass` for updates
- Uses OOP
- Optional Settings for each bot can be saved in JSON
- Based on cURL for speed
- Downloadable via composer
- Tested via GitHub Actions
- Has examples of working bots
- Telegram Errors as PHP Exceptions
- Up to date
- Documented

## Installation
[Installation Guide](https://muaath5.github.io/SimpleBotAPI/Installation)

## Simple Usage
### Creating bot
```php
use SimpleBotAPI/TelegramBot;
use SimpleBotAPI/BotSettings;

$Bot = new TelegramBot('bot_token');
```
More documentation [here](https://muaath5.github.io/SimpleBotAPI/CreatingBot)

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
    'chat_id' => '@MuaathBots',
    'text' => 'This post was sent by the bot..'
]);

$my_channel_info = $Bot->getChat([
    'chat_id' => '@MuaathBots',
]);
echo "Your channel (@{$my_channel_info->username}) ID: {$my_channel_info->id}";
```

**Note:** There's NO PROBLEM if `methodName` was in upper or lower case.

## Contibuting
You can contribute by:
- Reporting a bug.
- Suggesting a feature.
- Improving code style.
- Completing the TODO List.
- Adding more examples to `examples/`.
- Documenting undocumented topics in `docs/`.
- Updating the library to the next version of Bot API (if needed).
- Adding more tests in `test/` folder.

## Contact me
[![@Muaath_5 on Telegram](https://img.shields.io/badge/Telegram-@Muaath_5-blue)](https://t.me/Muaath_5)
[![muaath1428@hotmail.com](https://img.shields.io/badge/Email-muaath1428@hotmail.com-orange)](mailto:muaath1428@hotmail.com)

## Examples
Currently, There're three examples in `examples/` folder:
- Echo Bot, Which sends same as you sent
- Weclome Bot, Which sends Welcome & ID of user who joins the group
- Contact Me Bot, Which is a Bot be same as you, You'll receive messages & Reply
- FAQ Bot, Which has one message & buttons to swap between questions, And you can see answers.

You can see a full example [here](https://muaath5.github.io/SimpleBotAPI/FullExample)

**All of these examples doesn't need any databases or files to storage, Only Settings file.**

## TODO List
- [x] API via HTTP response in webhooks method
- [x] Using namespace for exceptions
- [x] Documenting sending message with reply markup
- [x] Add webhook authorization
- [x] Auto-store for bot users
- [x] Add JSON Storage can be used by bot
- [x] Method that posts to all bot users
- [X] Add logging
- [ ] Add DB Storage can be used by bot
- [ ] Document missed things

## License
[![Licence: GPL v3.0](https://img.shields.io/badge/Licence-GPL%20v3.0-green)](LICENCE)
GPL-3.0, In LICENCE file.