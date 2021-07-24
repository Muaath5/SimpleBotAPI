<?php
/**
 * A class should be exists for each bot that processes all updates either webhook or long-polling.
 * @version Bot API 5.3
 */
abstract class UpdatesHandler
{
    private TelegramBot $Bot;
    public function __construct(TelegramBot $bot)
    {
        $this->Bot = $bot;
    }

    abstract public function MessageHandler(object $message) : bool;
    abstract public function EditedMessageHandler(object $edited_message) : bool;

    abstract public function ChannelPostHandler(object $channel_post) : bool;
    abstract public function EditedChannelPostHandler(object $edited_channel_post) : bool;

    abstract public function InlineQueryHandler(object $inline_query) : bool;
    abstract public function ChosenInlineQueryHandler(object $chosen_inline_query) : bool;

    abstract public function CallbackQueryHandler(object $callback_query) : bool;

    abstract public function PollHandler(object $poll_answer) : bool;
    abstract public function PollAnswerHandler(object $poll_answer) : bool;

    abstract public function ShippingQueryHandler(object $shipping_query) : bool;
    abstract public function PreCheckoutQueryHandler(object $pre_checkout_query) : bool;

    abstract public function MyChatMemberHandler(object $my_chat_member) : bool;
    abstract public function ChatMemberHandler(object $chat_member) : bool;
}