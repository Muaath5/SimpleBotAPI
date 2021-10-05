<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use SimpleBotAPI\TelegramBot;

final class TelegramBotTest extends TestCase
{
    public function testGetMe() : void
    {
        $this->assertNotFalse(getenv('TEST_BOT_TOKEN'), 'TEST_BOT_TOKEN Environment variable is empty');
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'));
        $this->assertEquals('Muaath_5_Test_Bot', $Bot->GetMe()->username);
    }
}