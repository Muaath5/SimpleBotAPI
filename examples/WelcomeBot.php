<?php
# This file should be a webhook on your website

namespace SimpleBotAPI\Examples;

# Reuire autoload.php
//require __DIR__ . '/vendor/autoload.php';

use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\UpdatesHandler;
use SimpleBotAPI\BotSettings;

# The webhook url should be:
# https://mywebsite.com/echo-bot.php?token={bot_token}
#
# So, The webhook will check authentication
# We will store BOT_TOKEN environment variable
# Also you need to store logs chat ID in LOGS_CHAT_ID section

# Check authentication
if ($_REQUEST['token'] == getenv('BOT_TOKEN'))
{
    $Bot = new TelegramBot(getenv('BOT_TOKEN'), new WelcomeBot(getenv('LOGS_CHAT_ID'), new BotSettings()));

    # Process Webhook Update
    $Bot->OnWebhookUpdate();
}

# Note: When you send setWebhook method, Take care that chat_member updates should be allowed
class WelcomeBot extends UpdatesHandler
{
    protected int|float|string $LogsChatID;

    public function __construct(int|float|string $logs_chat_id)
    {
        $this->LogsChatID = $logs_chat_id;
    }

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
                $this->Bot->SendMessage([
                    'chat_id' => $this->LogsChatID,
                    'text' => "<b>Error</b>\n$ex"
                ]);
                return false;
            }
        }
        return true;
    }
}