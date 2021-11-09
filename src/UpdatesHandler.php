<?php

namespace SimpleBotAPI;

use SimpleBotAPI\TelegramBot;

/**
 * A class should be exists for each bot that processes all updates either webhook or long-polling.
 * @version Bot API 5.3
 */
class UpdatesHandler
{
    protected ?TelegramBot $Bot;
    public function __construct(?TelegramBot $bot = null)
    {
        $this->Bot = $bot;
    }

    public function SetBot(?TelegramBot $bot = null)
    {
        $this->Bot = $bot;
    }

    public function MessageHandler(object $message) : bool
    {
        return false;
    }

    public function EditedMessageHandler(object $edited_message) : bool
    {
        return false;
    }

    public function ChannelPostHandler(object $channel_post) : bool
    {
        return false;
    }

    public function EditedChannelPostHandler(object $edited_channel_post) : bool
    {
        return false;
    }

    public function InlineQueryHandler(object $inline_query) : bool
    {
        return false;
    }

    public function ChosenInlineQueryHandler(object $chosen_inline_query) : bool
    {
        return false;
    }


    public function CallbackQueryHandler(object $callback_query) : bool
    {
        return false;
    }


    public function PollHandler(object $poll_answer) : bool
    {
        return false;
    }

    public function PollAnswerHandler(object $poll_answer) : bool
    {
        return false;
    }


    public function ShippingQueryHandler(object $shipping_query) : bool
    {
        return false;
    }

    public function PreCheckoutQueryHandler(object $pre_checkout_query) : bool
    {
        return false;
    }


    public function MyChatMemberHandler(object $my_chat_member) : bool
    {
        return false;
    }

    public function ChatMemberHandler(object $chat_member) : bool
    {
        return false;
    }


    public function ChatJoinRequestHandler(object $chat_join_request) : bool
    {
        return false;
    }
}