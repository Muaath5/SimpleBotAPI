# Updates
## Webhook
### Registering webhook
There's two ways
#### 1. Use `$Bot->SetBotWebhook()` method [Suggested]
If you used this way, The updates will be checked if it was from Telegram, By checking `$_GET['token_hash']` if it equals to `hash(HashingMethod, Token)`

#### 2. Native `setWebhook` method
If you wanted this, You can run it in Postman Or browser.

### Receiving
```php
$success = $Bot->OnWebhookUpdate();
if (!$success) {
    error_log('Bot API version is out-dated, Or fake updates are coming!');
}
```
`$success` will be boolean, So you can log or add to your statistics count of unhandled updates for the bot.

**Note:** If you don't want fake updates, You can use `$Bot->CheckAuthorization()`, Only on PHP web apps


### Deleting
You **can't** use both Long-polling & Webhook, So if you want to move from webhook to long-polling use this:
```php
$Bot->DeleteWebhook();
```

## Long-polling (`getUpdates` method)
If you want Long-polling that stops after sometime:
```php
$stop_time = strtotime('+10 minutes');
while (time() < $stop_time)
{
    $Bot->ReceiveUpdates();
}
```

Or let bot runs forever:
```php
while (true)
{
    $Bot->ReceiveUpdates();
}
```

[Go to next docuemnt?](https://muaath5.github.io/SimpleBotAPI/BotSettings)