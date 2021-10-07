# Telegram Payments
Bots can receive payments via bots.

## How can I receive money?
You should set a payment provider in @BotFather, Then you'll need to fill your card info.
Then you'll receive money there.

## What updates should I use?
You can say that processing payments run in these steps:
1. Your bot send an invoice (Type of messages contains _Pay_ button and what you'll sell)
2. Bot receives shipping info (_Optional_, Your bot may not request the shipping info)
   - If shipping info was incorrect or can't delivery, You can not accept it and let user retype the info
3. Your bot show what delivery options avaliable
4. Your bot receive update `pre_checkout_query`, Your bot should check if all info is good & If item still exists in the shop
5. Bot will receive the receipt!

## Example
There is a test payment bot in [Muaath5/MuaathBots](https://github.com/Muaath5/MuaathBots/bots/src/TestPaymentBot.php)