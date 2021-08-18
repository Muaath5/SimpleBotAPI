<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\TelegramBot;

final class TelegramBotTest extends TestCase
{
    public function testGetMe() : void
    {
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'));
        $this->assertEquals(getenv('TEST_BOT_USERNAME'), $Bot->GetMe()->username);
    }

    // public function testAutoHandleFloodException() : void
    // {
    //     # By default, BotSettings has AutoHandleFloodException = true
    //     $Settings = new BotSettings();
    //     $Settings->AutoHandleFloodException = true;
    //     $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), $Settings);

    //     $Bot->SendMessage([
    //         'chat_id' => 1265170068,
    //         'text' => '<code>testAutoHandleFloodException</code> started, To test (Auto handle flood exceptions), Not for abuse the Bot API.',
    //         'parse_mode' => 'HTML'
    //     ]);
    //     for ($i = 1; $i <= 50; $i++)
    //     {
    //         $lastMessage = $Bot->SendMessage([
    //             'chat_id' => 1265170068,
    //             'text' => "Flood test number {$i}"
    //         ]);
    //     }
    //     $this->assertIsInt($lastMessage->message_id);
    // }
}