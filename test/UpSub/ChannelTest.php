<?php

use UpSub\Client;
use UpSub\Message;
use UpSub\Channel;
use PHPUnit\Framework\TestCase;

require_once 'CurlMock.php';

class ChannelTest extends TestCase
{
    /**
     * Instance of the upsub client
     * @var Client
     */
    private $client;

    /**
     * Setup before testrun
     */
    public function __construct()
    {
        $this->client = new Client('http://localhost:4400', [
            'dependencies' => [
                'curl' => CurlMock::class
            ]
        ]);
    }

    /**
     * Should send message from channel object
     */
    public function testShouldSendMessageFromChannel()
    {
        $channel = $this->client->channel('prefix-channel');
        $response = $channel->send('event', 'data');
        $this->assertEquals(
            Message::text('prefix-channel/event', 'data')->encode(),
            $response->response
        );
    }

    /**
     * Should send message as a batch if multiple channels is bound
     */
    public function testShouldSendBatchOnMultiChannel()
    {
        $multiChannel = $this->client->channel('channel-1', 'channel-2');
        $response = $multiChannel->send('event', 'payload');

        $batch = Message::batch([
            Message::text('channel-1/event', 'payload'),
            Message::text('channel-2/event', 'payload')
        ]);

        $this->assertEquals(
            $batch->encode(),
            $response->response
        );
    }
}
