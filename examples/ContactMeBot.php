<?php
# This file should be a webhook on your website

# Reuire autoload.php
require __DIR__ . '/vendor/autoload.php';

use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\UpdatesHandler;

# The webhook url should be:
# https://mywebsite.com/echo-bot.php?token={bot_token}
#
# So, The webhook will check authentication
# We will store BOT_TOKEN environment variable

# Check authentication
if ($_REQUEST['token'] == getenv('BOT_TOKEN'))
{
    $Bot = new TelegramBot(getenv('BOT_TOKEN'), new ContactMeBot(getenv('MESSAGES_CHAT_ID'), [126008640]));

    # Process Webhook Update
    $Bot->OnWebhookUpdate(file_get_contents('php://input'));
}

class ContactMeBot extends UpdatesHandler
{
    protected int|float|string $MessagesChatID;
    private array $BotAdmins;

    public function __construct(int|float|string $messages_chat_id, array $bot_admins)
    {
        $this->MessagesChatID = $messages_chat_id;
        $this->BotAdmins = $bot_admins;
    }

    # Write the handler for updates that your bot needs
    public function MessageHandler(object $message): bool
    {
        # The bot class will be stored in $this->Bot
        if (property_exists($message, 'text'))
        {
            # If admin
            if (in_array($message->from->id, $this->BotAdmins))
            {
                if (property_exists($message, 'reply_to_message'))
                {
                    $reply_chat_id = intval($message->reply_to_message->reply_markup->inline_keyboard[0][0]->text);
                    $reply_message_id = intval($message->reply_to_message->reply_markup->inline_keyboard[1][0]->text);

                    $this->Bot->CopyMessage([
                        'from_chat_id' => $message->chat->id,
                        'message_id' => $message->message_id,
                        'chat_id' => $reply_chat_id,
                        'reply_to_message_id' => $reply_message_id,

                    ]);
                }
                else
                {
                    $this->Bot->SendMessage([
                        'chat_id' => $this->MessagesChatID,
                        'text' => 'You should reply on a message!'
                    ]);
                }
            }

            # Commands
            if ($message->text[0] == '/')
            {
                switch ($message->text)
                {
                    case '/start':
                        $this->Bot->SendMessage([
                            'chat_id' => $message->chat->id,
                            'text' => 'Send message to contact with SimpleBotAPI creator, I may reply..',
                            'reply_to_message_id' => $message->message_id,
                            'reply_markup' => [
                                'force_reply' => true,
                                'input_field_placeholder' => 'Contact Me!',
                                'selective' => true
                            ]
                        ]);
                        break;

                    default:
                        $this->Bot->SendMessage([
                            'chat_id' => $message->chat->id,
                            'text' => 'Sorry, Command not found!'
                        ]);
                        break;
                }
            }
            else
            {
                # Copy message to the owner of the bot
                $this->Bot->CopyMessage([
                    'chat_id' => $this->MessagesChatID,
                    'from_chat_id' => $message->chat->id,
                    'message_id' => $message->message_id,

                    # The bot will store user Data on the buttons
                    'reply_markup' => json_encode(['inline_keyboard' => [
                        [['text' => $message->chat->id, 'url' => property_exists($message->chat, 'username') ? "https://t.me/{$message->chat->username}" : 'https://google.com/']],
                        [['text' => $message->message_id, 'url' => 'https://google.com']]
                        [['text' => property_exists($message->chat, 'title') ? $message->chat->title : $message->chat->first_name . ' ' . $message->chat->last_name, 'url' => 'https://google.com/']]
                    ]])
                ]);
            }
        }
        return true;
    }
}