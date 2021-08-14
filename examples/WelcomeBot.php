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
    $Bot = new TelegramBot(getenv('BOT_TOKEN'), new EchoBot());

    # Process Webhook Update
    $Bot->OnWebhookUpdate(file_get_contents('php://input'));
}

# Note: When you send setWebhook method, Take care that chat_member updates should be allowed
class EchoBot extends UpdatesHandler
{
    # Write the handler for updates that your bot needs
    public function MessageHandler(object $message): bool
    {
        # The bot class will be stored in $this->Bot
        if (property_exists($message, 'text'))
        {
            if ($message->text == '/start' || '/start@USERNAME_OF_THE_WELCOME_BOT')
            $this->Bot->SendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'This bot will send welcome to anyone joins the group, Only add me to the group.\n\n P.S. I DON\'T need to be admin!'
            ]);
        }
        return true;
    }

    public function ChatMemberHandler(object $chat_member): bool
    {
        if ($chat_member->new_chat_member->status == 'member')
        {
            try
            {
                $this->Bot->SendMessage([
                    'chat_id' => $chat_member->chat->id,
                    'text' => "Weclome to {$chat_member->chat->title}, Your ID is: {$chat_member->from->id}"
                ]);
            }
            catch (\Exception $ex)
            {
                # You need to store your ID in MY_USER_ID section
                $this->Bot->SendMessage([
                    'chat_id' => getenv('MY_USER_ID'),
                    'text' => "<b>Error</b>\n$ex"
                ]);
                return false;
            }
        }
        return true;
    }
}