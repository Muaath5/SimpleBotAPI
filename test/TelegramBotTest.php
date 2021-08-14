<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use SimpleBotAPI\TelegramBot;

final class TelegramBotTest extends TestCase
{
    public function testGetMe() : void
    {
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'));
        $this->assertEquals(getenv('TEST_BOT_USERNAME'), $Bot->GetMe()->username);
    }

    public function testFlood() : void
    {
        $this->expectException('SimpleBotAPI\TelegramFloodException');
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'));
        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'Testing started for SimpleBotAPI'
        ]);
        for ($i = 1; $i <= 100; $i++)
        {
            $Bot->SendMessage([
                'chat_id' => 1265170068,
                'text' => "Flood test number {$i}"
            ]);
        }
    }
}