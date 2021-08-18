<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Xml\FailedSchemaDetectionResult;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\Examples\FAQBot;

final class FAQBotTest extends TestCase
{
    public function testGetMe() : void
    {
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), new BotSettings());
        $this->assertEquals(getenv('TEST_BOT_USERNAME'), $Bot->GetMe()->username);
    }

    public function testRun() : void
    {
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), BotSettings::Import(__DIR__ . '/faq-bot-settings.json'));

        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'FAQBotTest.php started!'
        ]);
        $stop_time = strtotime('+10 minutes');
        while (time() != $stop_time)
        {
            $Bot->ReceiveUpdates();
        }
        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'FAQBot.php was stopped..'
        ]);
    }
}