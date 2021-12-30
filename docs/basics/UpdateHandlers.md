# Update Handlers
What is the update handlers?
It's a class that has methods will be called if an update received.

The _examples_ folder has 4 examples, You can see the update handlers there.

## How to create an UpdateHandler?
Only create a class that inherit from `UpdateHandler`.
It has methods will be called in case of update like:
`CallbackQueryHandler`, `MessageHandler`, and `EditedChannelPostHandler`
You should override the methods, and put your own code using `$this->Bot` variable

## How can I use my bot in my Update handler?
There's a variable called `$Bot` in each `UpdateHandler` instance
Use it to reply on messages, sending broadcasts, and any other requests

[Go to next document?](https://muaath5.github.io/SimpleBotAPI/ReceivingUpdates)