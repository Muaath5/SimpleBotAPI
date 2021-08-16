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
    $Bot = new TelegramBot(getenv('BOT_TOKEN'), new FAQBot(
        [
            'What is SimpleBotAPI Library?',
            'Why SimpleBotAPI Library?',
            'Can I use SimpleBotAPI Library in long-polling?',
            'How to install SimpleBotAPI?',
            'Has SimpleBotAPI PHP Unit Tests?',
            'What is the classes implemented in the Library?'
        ],
        [
            'It\'s a Bot API Library in PHP, Based on <i>cURL</i>, With code are updated, So you can use any method even library wasn\'t updated',
            'Because it\' easy to install & use, Also it has full documentation & examples for almost everything!',
            'Currently, No. But it\'ll be added in the next update',
            'Via composer:\n<code>composer require muaath5/simple-bot-api</code>',
            'Yes, It runs by GitHub workflow',
            '<code>TelegramBot, UpdatesHandler, TelegramException, TelegramFloodException</code>, And <code>TelegramChatMigratedException</code>'
        ]
    ));

    # Process Webhook Update
    $Bot->OnWebhookUpdate(file_get_contents('php://input'));
}

class FAQBot extends UpdatesHandler
{
    public array $Questions;
    public array $Answers;

    public function __construct(array $questions, array $answers)
    {
        $this->Questions = $questions;
        $this->Answers = $answers;
    }

    # Write the handler for updates that your bot needs
    public function MessageHandler(object $message): bool
    {
        $this->Bot->SendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'These are FAQ Questions, Choose one from these questions to get answer',
            'reply_markup' => ['inline_keyboard' => $this->GetButtonsByPageID(0)]
        ]);
        return true;
    }

    public function CallbackQueryHandler(object $callback_query): bool
    {
        if (str_starts_with($callback_query->data, 'page_'))
        {
            $page_id = intval(substr($callback_query->data, 7, strlen($callback_query->data)));
            if (property_exists($callback_query, 'message'))
            {
                $this->Bot->EditMessageReplyMarkup([
                    'chat_id' => $callback_query->message->chat->id,
                    'message_id' => $callback_query->message->message_id,
                    'reply_markup' => ['inline_keyboard' => $this->GetButtonsByPageID($page_id)]
                ]);
            }
        }

        if (str_starts_with($callback_query->data, 'answer_'))
        {
            $answer_id = intval(substr($callback_query->data, 7, strlen($callback_query->data)));
            if (property_exists($callback_query, 'message'))
            {
                $this->Bot->EditMessageReplyMarkup([
                    'chat_id' => $callback_query->message->chat->id,
                    'message_id' => $callback_query->message->message_id,
                    'reply_markup' => ['inline_keyboard' => [
                        [['text' => 'Go Back', 'callback_data' => 'page_'.$this->GetPageIDByAnswerID($answer_id)]]
                    ]]
                ]);
            }
        }

        $this->Bot->AnswerCallbackQuery([
            'callback_query_id' => $callback_query->id,
            'text' => 'Error!\nCommand not found, Contact with developer',
            'show_alert' => true
        ]);
        return false;
    }

    public function GetPageIDByAnswerID(int $answer_id) : int
    {
        return ($answer_id / 5) + ($answer_id % 5 == 0 ? 0 : 1);
    }

    public function GetButtonsByPageID(int $page_id) : array
    {
        $buttons = [];

        for ($i = 0; $i < 5; $i++)
        {
            array_push($buttons, [['text' => $this->Questions[$i], 'callback_data' => "answer_$i"]]);
        }

        $pages_count = count($buttons) / 5 + (count($buttons) % 5 == 0 ? 0 : 1);
        if ($page_id == 0)
        {
            array_push($buttons, [['text' => '==>', 'callback_data' => 'page_1']]);
        }
        else if ($page_id == $pages_count-1)
        {
            array_push($buttons, [['text' => '==>', 'callback_data' => 'page_' . $pages_count-2]]);
        }
        else
        {
            array_push($buttons, [['text' => '<==', 'callback_data' => 'page_' . $page_id-1], ['text' => '==>', 'callback_data' => 'page_' . $page_id+1]]);
        }

        return $buttons;
    }
}