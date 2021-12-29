# Full Example (Long polling)
_Long-polling_ means running endless `while` and using `getUpdates` method

```php
require_once __DIR__ . '/vendor/autoload.php';

define('SETTINGS_FILE_PATH', '/app/bots/MyBotSettings.json');

use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\UpdatesHandler;

# You can copy any of Update handlers in bot examples directory
class MyBot extends UpdatesHandler
{
    
}

$Bot = new TelegramBot(getenv('TELEGRAM_BOT_TOKEN'), new MyBot());

echo "Bot started!\n";
while (true)
{
    $Bot->ReceiveUpdates();
}
```