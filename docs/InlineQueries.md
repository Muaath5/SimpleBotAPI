# Inline Queries
## Receiving inline queries
Use handler `InlineQueryHandler`

## Answering inline queries
Use method `answerInlineQuery` like this:

## Buttons related to inline query

## Examples:
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