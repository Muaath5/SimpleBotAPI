# Callback Queries
In the previous document, We learnt how to send message with inline keyboard.

But here, We'll learn how to handle an update if a button clicked.

## Receiving
In sending, Bot will receive update only if the button has `callback_data` field.
The bot will receive the update contains:
- Callback Data (To know which button was clicked)
- Message (Which has that button, bot can read or edit the message)
- User (Who clicked the button)

Handle `callback_query` update using `CallbackQueryHandler` method
You'll need to use `answerCallbackQuery` to show a text or an alert to the user

## Example
```php
define('BOT_ADMINS', [123, 456, 789]);
public function CallbackQueryHandler($callback_query) : bool
{
    $answer = 'Invalid query';
    if ($callback_query->data == 'is_admin')
    {
        if (array_search(BOT_ADMINS, $callback_query->from->id) !== false)
        {
            
        }
    }
    $this->Bot->AnswerCallbackQuery();
    return true;
}
```