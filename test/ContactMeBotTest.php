<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use SimpleBotAPI\Examples\ContactMeBot;
use SimpleBotAPI\TelegramBot;

final class ContactMeBotTest extends TestCase
{
    public function testRun() : void
    {
        $this->assertNotFalse(getenv('TEST_BOT_TOKEN'), 'TEST_BOT_TOKEN Environment variable is empty');
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), new ContactMeBot(1265170068, [1265170068]));
        
        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'ContactMeBotTest.php started!'
        ]);
        $stop_time = strtotime('+3 minutes');
        while (time() < $stop_time)
        {
            $Bot->ReceiveUpdates();
        }
        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'ContactMeBotTest.php stopped.'
        ]);

        $this->assertEquals(true, true);
    }
}