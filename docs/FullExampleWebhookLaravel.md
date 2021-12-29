# Full Example (Webhook with Laravel)
**Note:** This code may not run in versions later than Laravel 8
```php
use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\UpdatesHandler;

# You can copy any of Update handlers in bot examples directory
class MyBot extends UpdatesHandler
{
    
}

# Telegram sends updates in POST requests
# You can set any route you want
Route::post('/bot' function () {
    $Bot = new TelegramBot(getenv('TELEGRAM_BOT_TOKEN'), new MyBot(), new BotSettings());
    $Bot->OnWebhookUpdate(); // Processing received update
});
```