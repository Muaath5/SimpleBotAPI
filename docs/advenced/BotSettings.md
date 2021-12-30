# Bot Settings

## What is BotSettings?
It's some settings that are provided by the library, To make the bot coding easy for you.
1. Add ability to auto-handle for exceptions
2. Save your bot from duplicate and un-needed updates

## Bot Settings features
Bot Settings feature:
- Auto handle for flood & chat migrated exceptions.
- Auto handle for duplicate & unneeded updates.
- Using local Bot API Server.

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

And you can import it!
### Importing Bot Settings
```php
$Settings = BotSettings::Import('/home/main/bots/MyBot/MyBotSettings.json');
```