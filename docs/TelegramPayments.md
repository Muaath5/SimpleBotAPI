# Telegram Payments
Bots can receive payments via bots.

## How can I receive money?
You should set a payment provider in [@BotFather](https://t.me/BotFather), Then you'll need to fill your card info.
Then you'll receive money on your payment provider.

## What updates should I use?
You can say that processing payments run in these steps:
1. Your bot send an invoice (Type of messages contains _Pay_ button and what you'll sell)
2. Bot receives shipping info (_Optional_, Your bot may not request the shipping info)
   - If shipping info was incorrect or can't delivery, You can not accept it and let user retype the info
3. Your bot show what delivery options avaliable
4. Your bot receive update `pre_checkout_query`, Your bot should check if all info is good & If item still exists in the shop
5. Bot will receive the receipt!

## Custom keyboard on invoices
You can provide custom keyboard in invoices & put buttons like getting help or trying on inline
Note: Pay buttons must be in first row and first column
```php
$total = 516.75;
$invoice = [

];
$keyboard = [
   'inline_keyboard' => [
      [
         [
            'text' => "Pay {$total}",
            'pay' => true
         ]
      ],
      [
         [
            'text' => 'Get help',
            'callback_data' => 'payment_help'
         ],
         [
            'text' => '✉ My Privacy',
            'callback_data' => 'payment_privacy'
         ]
      ]
   ]
];
$this->Bot->SendInvoice([
   'chat_id' => $message->chat->id,
   'invoice' => json_encode($invoice),
   'reply_markup' => json_encode($keyboard)
]);
```

## Advices & Suggestions
1. Keep your account safe with a Cloud password, So no scammers get your money.
2. Keep your bot token very secret, and keep the code closed source
3. Don't share your provider token with anyone
4. It's preferred to have logs channel you log any payment there with all info, So you can handle any incorrect payment
5. When testing your bot, use a test provider token, so no money will be paid
6. It's good to add `/privacy` & `/help` on your bot, so everything be clear to the user
7. Add an info message if it was a real payment

## Example
There is a test payment bot in [Muaath5/MuaathBots](https://github.com/Muaath5/MuaathBots/bots/src/TestPaymentBot.php)