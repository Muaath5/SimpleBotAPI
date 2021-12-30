# Creating Bot
First, You should include the `TelegramBot` class, which has the methods
```php
require_once __DIR__.'/vendor/autoload.php';

use SimpleBotAPI\TelegramBot;

$Bot = new TelegramBot('<BOT_TOKEN>');
```

And then sending requests, only call any method in Bot API with and array as parameters

```php
$botInfo = $Bot->GetMe();
$webhookInfo = $Bot->GetWebhookInfo();
try {
    $Bot->SendMessage([
        'chat_id' => 1265170068,
        'text' => 'Testing message'
    ]);
} catch (\Exception $ex) {
    error_log($ex);
}
```

[Go to next document?](https://muaath5.github.io/SimpleBotAPI/UpdateHandlers)