# Creating Bot
First, You should include the `TelegramBot` class
```php
include __DIR__ . 'vendor/autoload.php';

use SimpleBotAPI\TelegramBot;

$Bot = new TelegramBot('<BOT_TOKEN>');
```

And then sending requests, Only call any method in Bot API with and array as parameters

```php
$botInfo = $Bot->GetMe();
$webhookInfo = $Bot->GetWebhookInfo();
if ($botInfo->id > 1000000000 && $webhookInfo->pending_updates == 0)
{
    $Bot->SetWebhook([
        'url' => 'https://tgbot.herokuapp.com/webhook.php',
        'max_connections' => 10
    ]);
}
else
{
    $Bot->SendMessage([
        'chat_id' => 1265170068,
        'text' => '<b>Error</b>, Bot doesn\'t has the needed terms',
        'parse_mode' => 'HTML'
    ]);
}
```

[Go to next document?](https://muaath5.github.io/SimpleBotAPI/UpdateHandlers)