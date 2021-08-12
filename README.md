# Simple Bot API
This is simple Telegram Bot API in PHP, Supports Bot API 5.3.

This Library should support all Bot API versions.

## Installation
Install it via composer:
```sh
composer require muaath5/simple-bot-api
```
## Using method
It should be in this format:
```
$Bot->{methodName}([
    '{paramName}' => {value},
    '{paramName2}' => {value2}
]);
```

Like this:
```php
$Bot->SendMessage([
    'chat_id' => '@Muaath_Alqarni',
    'text' => 'Some post..'
]);

$my_channel_info = $Bot->getChat([
    'chat_id' => '@Muaath_Alqarni',
]);
```

The method has no problem if it was upper or lower case.

## Receiving updates
You should create a class that inherets from `UpdatesHandler` to handle all updates types, And do what ever you want.
Example of Bot that sends welcome to users:
```php
<?php

namespace MyBot1;

require '../vender/autoload.php';

use SimpleBotAPI\UpdatesHandler;
use SimpleBotAPI\TelegramBot;

class WelcomeBot extends UpdatesHandler
{
    private TelegramBot? Bot = null;

    public function __construct(TelegramBot $bot)
    {
        $this->Bot = $bot;
    }

    public function MessageHandler($message) : bool
    {
        if ($message->text === '/start')
        {
            $Bot->SendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'I am a bot that sends hello to anyone join in a group! Add me to the group!'
            ]);


        }
    }

    public function ChatMemberHandler($chat_member) : bool
    {
        switch ($chat_member->new_chat_member->status)
        {
            case 'member':
                # Send "Welcome" to the user!
                $Bot->SendMessage([
                    'chat_id' => $chat_member->chat->id,
                    'text' => "Welcome to {$chat_member->chat->title}!\nRead the pinned message for more info!"
                ]);
                break;
            
            case 'left':
                $Bot->SendMessage([
                    'chat_id' => $chat_member->chat->id,
                    'text' => "It was nice to meat you, {$chat_member->new_chat_member->user->first_name}!"
                ]);
                break;
        }
        
    }

    public function MyChatMemberHandler($chat_member) : bool
    {
        $Bot->SendMessage([
            'chat_id' => $chat_member,
            'text' => 'I will send Welcome to all users join!'
        ]);
    }
}
```

**Note:** You should take care of composer namespaces via `autoload` field in `composer.json` file!

And the source of the webhook page (`webhook.php`):
```php
<?php
namespace MyBot1;

require '../vender/autoload.php';

use SimpleBotAPI\TelegramBot;
use MyBot1\WelcomeBot;

$Bot = new TelegramBot(getenv('BOT_TOKEN'));
$Bot->SetUpdatesHandler(new WelcomeBot($Bot));
$Update = json_decode(file_get_contents('php://input'));
if (!empty($Update))
    $Bot->OnUpdate($Update);
```