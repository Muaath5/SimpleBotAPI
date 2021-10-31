# Callback Queries
In the previous document, We learnt how to send message with inline keyboard.

But here, We'll learn how to handle an update if a button clicked.

## Sending
In sending, Bot will receive update only if the button has `callback_data` field.
The bot will receive the update contains:
- Callback Data (To know where was the button)
- Message (Bot may read or edit the message)
- User (Who clicked the button)

```php
$Bot->SendMessage([
    'chat_id' => '@PublicGroupTest',
    'text' => 'Edit your own settings'
]);
```