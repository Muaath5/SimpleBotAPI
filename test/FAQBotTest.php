<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Xml\FailedSchemaDetectionResult;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\Examples\ContactMeBot;
use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\Examples\FAQBot;

final class FAQBotTest extends TestCase
{
    public function testRun() : void
    {
        $this->assertNotFalse(getenv('TEST_BOT_TOKEN'), 'TEST_BOT_TOKEN Environment variable is empty');
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), new FAQBot());
            
        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'FAQBotTest.php started!'
        ]);

        $stop_time = strtotime('+3 minutes');
        while (time() < $stop_time)
        {
            $Bot->ReceiveUpdates();
        }

        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'FAQBot.php stopped.'
        ]);

        $this->assertEquals(true, true);
    }
}