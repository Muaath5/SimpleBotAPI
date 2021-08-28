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
        if (true)
        {
            $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), new BotSettings(new FAQBot()));
            
            $Bot->SendMessage([
                'chat_id' => 1265170068,
                'text' => 'FAQBotTest.php started!'
            ]);
            $stop_time = strtotime('+7 minutes');
            while (time() < $stop_time)
            {
                $Bot->ReceiveUpdates();
            }
            $Bot->SendMessage([
                'chat_id' => 1265170068,
                'text' => 'FAQBot.php was stopped..'
            ]);
        }  
        $this->assertEquals(true, true);
    }
}