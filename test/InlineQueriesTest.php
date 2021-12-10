<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use SimpleBotAPI\TelegramBot;
use SimpleBotAPI\Examples\InlineQueriesIndexBot;

final class InlineQueriesTest extends TestCase
{
    public function testInlineQueries() : void
    {
        $this->assertNotFalse(getenv('TEST_BOT_TOKEN'), 'TEST_BOT_TOKEN Environment variable is empty');
        $Bot = new TelegramBot(getenv('TEST_BOT_TOKEN'), new InlineQueriesIndexBot([
            '-1' => 'ERROR [400]: Invalid query ID',
            '1' => "Secret!!",
            '2' => "How did you discovered this?",
            '200' => 'SUCCESS [200]: Success!',
            '303' => 'REDIRECT [303]: Redirect',
            '404' => 'ERROR [404]: Not found',
            '406' => 'ERROR [406]: Update app',
            '509' => 'Author: Muaath **Alqarni**',
            '2013' => 'This was the year of creating Telegram!',
            '2019' => 'TAKE CARE :: COVID-19 HERE',
            '2021' => 'This is the year of creating @DIBgram!'
        ]));
        
        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'InlineQueriesTest.php started!'
        ]);

        $stop_time = strtotime('+3 minutes');
        while (time() < $stop_time)
        {
            $Bot->ReceiveUpdates();
        }

        $Bot->SendMessage([
            'chat_id' => 1265170068,
            'text' => 'InlineQueriesTest.php stopped.'
        ]);

        $this->assertEquals(true, true);
    }
}