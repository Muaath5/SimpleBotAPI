<?php
# This file should be a webhook on your website

namespace SimpleBotAPI\Examples;

# Reuire autoload.php
//require __DIR__ . '/vendor/autoload.php';

use SimpleBotAPI\BotSettings;
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
    $Bot = new TelegramBot(getenv('BOT_TOKEN'), new EchoBot(), new BotSettings());

    # Process Webhook Update
    $Bot->OnWebhookUpdate();
}

class EchoBot extends UpdatesHandler
{
    # Write the handler for updates that your bot needs
    public function MessageHandler(object $message): bool
    {
        # The bot class will be stored in $this->Bot
        if (property_exists($message, 'text'))
        {
            $this->Bot->SendMessage([
                'chat_id' => $message->chat->id,
                'text' => $message->text
            ]);
        }
        return true;
    }
}