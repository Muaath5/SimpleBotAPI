<?php
# This file should be a webhook on your website

namespace SimpleBotAPI\Examples;

# Reuire autoload.php
//require __DIR__ . '/vendor/autoload.php';

use SimpleBotAPI\BotSettings;
use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\UpdatesHandler;

# The webhook url should be:
# https://mywebsite.com/InlineQueriesBot.php?token={bot_token}

# Check authentication, Or maybe using library check authentication
if (isset($_REQUEST['token']))
{
    if ($_REQUEST['token'] == getenv('BOT_TOKEN'))
    {
        $Bot = new TelegramBot(getenv('BOT_TOKEN'), new InlineQueriesIndexBot([
            'Secret' => 'cce23ef_Qa98HIDUROV256bI0t',
            'Telegram' => 'Telegram is a partially open source messaging app started in 2013 by @Durov..'
        ]), new BotSettings());
        
        # Process Webhook Update
        $Bot->OnWebhookUpdate();
    }
}

class InlineQueriesIndexBot extends UpdatesHandler
{
    public array $Index;
    public function __construct(array $index) {
        $this->Index = $index;
    }

    # Write the handler for updates that your bot needs
    public function MessageHandler(object $message): bool
    {
        if (property_exists($message, 'via_bot'))
        {
            if ($message->via_bot->username == 'Muaath_5_Test_Bot')
                return true;
        }
        $this->Bot->SendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'Type any number, And bot will show its answers',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Try an inline query',
                            'switch_inline_query_current_chat' => '-1'
                        ]
                    ]
                ]
            ])
        ]);
        return true;
    }

    public function InlineQueryHandler(object $inline_query): bool
    {
        $result = [];
        if (isset($this->Index[$inline_query->query]))
        {
            $result = [
                [
                    'type' => 'article',
                    'id' => $inline_query->query,
                    'title' => "Answer of {$inline_query->query} query",
                    'description' => 'Click to get the answer',
                    'input_message_content' => [
                        'message_text' => $this->Index[$inline_query->query],
                        'parse_mode' => 'Markdown'
                    ]
                ]
            ];
        }
        $this->Bot->AnswerInlineQuery([
            'inline_query_id' => $inline_query->id,
            'cache_time' => 1000,
            'results' => json_encode($result),
            'switch_pm_text' => 'How to use the bot?',
            'switch_pm_parameter' => 'howtouse'
        ]);
        return true;
    }
}