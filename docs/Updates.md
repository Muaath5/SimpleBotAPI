# Updates
## Webhook
### Registering webhook

### Receiving
```php
$success = $Bot->OnWebhookUpdate();
if (!$success) {
    error_log('Bot API version is out-dated, Or fake updates are coming!');
}
```
`$success` will be boolean, So you can log or add to your statistics count of unhandled updates for the bot.

**Note:** If you don't want fake updates, You can use `$Bot->CheckAuthorization()`, Only on PHP web apps


## Long-polling (`getUpdates` method)
You can't use both Long-polling & Webhook, So if you want to move from webhook to long-polling use this:
```php
$Bot->DeleteWebhook();
```


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

===
Any suggestions? Talk to [@Muaath_5](https://t.me/Muaath_5)