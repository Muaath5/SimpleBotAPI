# Bot Settings

## What is BotSettings?
It's some settings that are provided by the library, To make the bot coding easy for you.

## Why BotSettings?
I've added `BotSettings` for two reasons:
1. Add ability to auto-handle for exceptions
2. Save your bot from duplicate and un-needed updates

## Bot Settings features
Bot Settings feature:
- Auto handle flood exception.
- Auto handle chat-migrated exception.
- Auto handle for duplicate updates.
- Auto handle for settings.
- Allowing only some updates.
- Add Updates timeout if you're using long-polling.

## BotSettings coding

### Creating a bot settings
```php
use SimpleBotAPI\BotSettings;

$MyBotSettings = new BotSettings();
$MyBotSettings->AllowedUpdates = ['message', 'my_chat_member', 'chat_member'];
```

You can export bot settings as JSON file like this:
### Exporting Bot Settings
```php
$Settings->Export('/home/main/bots/MyBot/MyBotSettings.json');
```

Or you can **not specify** path, So it'll be created in the folder your UpdatesHandler was there, And with name: `{YourUpdatesHandlerName}Settings.json`
```php
$Settings->Export();
```

And you can import it!
### Importing Bot Settings
```php
$Settings = BotSettings::Import('/home/main/bots/MyBot/MyBotSettings.json');
```

===
Any suggestions? Talk to [@Muaath_5](https://t.me/Muaath_5)