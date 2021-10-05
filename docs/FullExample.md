# Full Example

```php
# Include autoload.php
require_once __DIR__ . '/vendor/autoload.php';

define('SETTINGS_FILE_PATH', '/app/bots/MyBotSettings.json');

use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\UpdatesHandler;

# You can copy any of Update handlers in bot exanokes directory
class MyBot extends UpdatesHandler
{
    
}

if (!file_exists(SETTINGS_FILE_PATH))
{
    $Settings = new BotSettings(SETTINGS_FILE_PATH);
    $Settings->AutoHandleSettings = true;
    $Settings->AutoHandleDuplicateUpdates = true;
}
else
{
    $Settings = BotSettings::Import(SETTINGS_FILE_PATH);
}

$Bot = new TelegramBot(getenv('TELEGRAM_BOT_TOKEN'), new MyBot(), BotSettings::Import(''));
$Bot->OnWebhookUpdate();
```