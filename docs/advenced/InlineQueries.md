# Inline Queries
## Receiving inline queries
Use handler `InlineQueryHandler`

## Answering inline queries
Use method `answerInlineQuery`

## Buttons related to inline query
You can use `switch_inline_query` & `switch_inline_query_current_chat` buttons in inline keyboard to let user start an inline query.

More info will be in [keyboards documntation](https://muaath5.github.io/SimpleBotAPI/MessageWithKeyboards)

## Example
```php
public function InlineQueryHandler($inline_query)
{
    $this->Bot->AnswerInlineQuery([
        'inline_qeury_id' => $inline_query->id,
        'results' => json_encode([]),
        'switch_pm_text' => 'Create an account first'
        'switch_pm_parameter' => 'create_account'
    ]);
}
```

[Example](https://github.com/Muaath5/SimpleBotAPI/tree/examples/InlineQueriesIndexBot.php)