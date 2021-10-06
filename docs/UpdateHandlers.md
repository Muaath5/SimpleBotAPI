# Update Handlers
What is the update handlers?
It's a class that has methods, `TelegramBot` class will call the methods when an update received.

The main code of the bot should be there.

The _examples_ folder has 4 examples, You can see the update handlers there.

## How to create an UpdateHandler?
Only create a class that inherit from `UpdateHandler`.
And override the methods.

## How can I use my bot in my Update handler?
There's an `TelegramBot` variable called `$Bot`.
Use it to reply on messages.

[Go to next document?](https://muaath5.github.io/SimpleBotAPI/ReceivingUpdates)