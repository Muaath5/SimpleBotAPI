# Simple Bot API
This is simple Telegram Bot API in PHP, Supports Bot API 5.3.

This Library should support all Bot API versions.

## Using method
```php

$result = $Bot->
```

## Receiving updates
You should use `UpdatesHandler` to handle all updates types
Example of Bot that sends welcome to users:
```php
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
And the source of the webhook page (`webhook.php`):
```php
$Bot = new TelegramBot(getenv('BOT_TOKEN'));
$Bot->SetUpdatesHandler(new WelcomeBot($Bot));
$Update = json_decode(file_get_contents('php://input'));
if (!empty($Update))
    $Bot->OnUpdate($Update);
```