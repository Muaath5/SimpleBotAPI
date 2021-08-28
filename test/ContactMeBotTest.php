<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use SimpleBotAPI\BotSettings;
use SimpleBotAPI\Examples\ContactMeBot;
use SimpleBotAPI\TelegramBot;

final class ContactMeBotTest extends TestCase
{
    public function testRun() : void
    {
        if (false)
        {
            $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), new BotSettings(new ContactMeBot(1265170068, [1265170068])));
            
            $Bot->SendMessage([
                'chat_id' => 1265170068,
                'text' => 'ContactMeBotTest.php started!'
            ]);
            $stop_time = strtotime('+7 minutes');
            while (time() < $stop_time)
            {
                $Bot->ReceiveUpdates();
            }
            $Bot->SendMessage([
                'chat_id' => 1265170068,
                'text' => 'ContactMeBotTest.php was stopped..'
            ]);
        }

        $this->assertEquals(true, true);
    }
}