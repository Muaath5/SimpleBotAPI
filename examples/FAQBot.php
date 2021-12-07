<?php
# This file should be a webhook on your website

namespace SimpleBotAPI\Examples;

# Reuire autoload.php
// require __DIR__ . '/vendor/autoload.php';

use SimpleBotAPI\BotSettings;
use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\UpdatesHandler;

# The webhook url should be:
# https://mywebsite.com/echo-bot.php?token={bot_token}
#
# So, The webhook will check authentication
# We will store BOT_TOKEN environment variable

# Check authentication
if (isset($_REQUEST['token']))
{
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
            )
        );

        # Process Webhook Update
        $Bot->OnWebhookUpdate(file_get_contents('php://input'));
    }
}
        
class FAQBot extends UpdatesHandler
{
    public array $Questions = [
        'What is SimpleBotAPI Library?',
        'Why SimpleBotAPI Library?',
        'Can I use SimpleBotAPI Library in long-polling?',
        'How to install SimpleBotAPI?',
        'Has SimpleBotAPI PHP Unit Tests?',
        'What is the classes implemented in the Library?'
    ];

    public array $Answers = [  
        'It\'s a Bot API Library in PHP, Based on <i>cURL</i>, With code are updated, So you can use any method even library wasn\'t updated',
        'Because it\' easy to install & use, Also it has full documentation & examples for almost everything!',
        'Currently, No. But it\'ll be added in the next update',
        'Via composer:\n<code>composer require muaath5/simple-bot-api</code>',
        'Yes, It runs by GitHub workflow',
        '<code>TelegramBot, UpdatesHandler, BotSettings, TelegramException, TelegramFloodException</code>, And <code>TelegramChatMigratedException</code>'
    ];

    public int $PagesCount = -1;
    
    public function __construct(array $questions = [], array $answers = [])
    {
        $this->Questions = $questions != [] ? $questions : $this->Questions;
        $this->Answers = $answers != [] ? $answers : $this->Answers;
        $this->PagesCount = intdiv(count($this->Questions), 5) + (count($this->Questions) % 5 == 0 ? 0 : 1);
    }

    # Write the handler for updates that your bot needs
    public function MessageHandler(object $message): bool
    {
        $this->Bot->SendMessage([
            'chat_id' => $message->chat->id,
            'text' => "These are FAQ Questions, Choose one from these questions to get answer\n\n[Page 1/{$this->PagesCount}]",
            'reply_markup' => json_encode($this->GetButtonsByPageID(0))
        ]);
        return true;
    }

    public function CallbackQueryHandler(object $callback_query): bool
    {
        if (str_starts_with($callback_query->data, 'page_'))
        {
            $page_id = intval(substr($callback_query->data, 5, strlen($callback_query->data)));
            if (property_exists($callback_query, 'message'))
            {
                $page_id += 1; // To make it one-based, For
                $this->Bot->EditMessageText([
                    'chat_id' => $callback_query->message->chat->id,
                    'message_id' => $callback_query->message->message_id,
                    'text' => "These are FAQ Questions, Choose one from these questions to get answer\n\n[Page {$page_id}/{$this->PagesCount}]",
                    'reply_markup' => json_encode($this->GetButtonsByPageID($page_id-1))
                ]);
            }
        }
        else if (str_starts_with($callback_query->data, 'answer_'))
        {
            $answer_id = intval(substr($callback_query->data, 7, strlen($callback_query->data)));
            if (property_exists($callback_query, 'message'))
            {
                $this->Bot->EditMessageText([
                    'chat_id' => $callback_query->message->chat->id,
                    'message_id' => $callback_query->message->message_id,
                    'text' => "<b>{$this->Questions[$answer_id]}</b>\n\n{$this->Answers[$answer_id]}",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode(['inline_keyboard' => [
                        [['text' => 'Go Back', 'callback_data' => 'page_'.$this->GetPageIDByAnswerID($answer_id)]]
                    ]])
                ]);
            }
        }
        else
        {
            $this->Bot->AnswerCallbackQuery([
                'callback_query_id' => $callback_query->id,
                'text' => 'Error!\nCommand not found, Contact with developer',
                'show_alert' => true
            ]);
        }

        $this->Bot->AnswerCallbackQuery([
            'callback_query_id' => $callback_query->id
        ]);
        return false;
    }

    public function GetPageIDByAnswerID(int $answer_id) : int
    {
        // Based-zero
        if ($answer_id == 0)
        {
            return 0; // The first page
        }
        return (($answer_id / 5) + ($answer_id % 5 == 0 ? 0 : 1)) - 1;
    }

    public function GetButtonsByPageID(int $page_id = 0) : array
    {
        $buttons = ['inline_keyboard' => []];

        
        for ($i = $page_id * 5; $i < ($page_id * 5 + 5) && $i < count($this->Questions); $i++)
        {
            array_push($buttons['inline_keyboard'], [['text' => $this->Questions[$i], 'callback_data' => "answer_$i"]]);
        }
        

        if ($page_id == 0)
        {
            array_push($buttons['inline_keyboard'], [['text' => '==>', 'callback_data' => 'page_1']]);
        }
        else if ($page_id == $this->PagesCount-1)
        {
            array_push($buttons['inline_keyboard'], [['text' => '<==', 'callback_data' => 'page_' . $page_id-1]]);
        }
        else
        {
            array_push($buttons['inline_keyboard'], [['text' => '<==', 'callback_data' => 'page_' . $page_id-1], ['text' => '==>', 'callback_data' => 'page_' . $page_id+1]]);
        }

        return $buttons;
    }

    public function InlineQueryHandler(object $inline_query): bool
    {
        # In inline will be search ability, Also to answer about some FAQ Questions in other chats
        $keys = array_keys($this->Questions, $inline_query->query);

        $results = [];

        foreach ($keys as $key)
        {
            $answer = array_search($key, $keys);
            array_push($results, [
                'id',
                'title',
                'description'
            ]);
        }

        $this->Bot->AnswerInlineQuery([
            'inline_query_id' => $inline_query->id,
            'results' => $results,
            'cache_time' => 5000
        ]);
        return true;
    }
}