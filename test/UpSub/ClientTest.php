<?php

use UpSub\Client;
use UpSub\Message;
use UpSub\Channel;
use PHPUnit\Framework\TestCase;

require_once 'CurlMock.php';

class ClientTest extends TestCase
{
    /**
     * Instance of the UpSub Client
     * @var Client
     */
    protected $client;

    /**
     * Setup the UpSub Client before each test
     */
    public function __construct()
    {
        $this->client = new Client('http://localhost:4400', [
            'appID' => 'app-id',
            'secret' => 'secret',
            'name' => 'name',
            'dependencies' => [
                'curl' => CurlMock::class
            ]
        ]);
    }

    /**
     * Should set APP_KEY and SECRET_KEY as HTTP Headers
     */
    public function testShouldSetHTTPHeaders()
    {
        $response = $this->client->send('some-channel', [
            'some' => 'data'
        ]);

        $this->assertArrayHasKey('upsub-app-id', $response->headers);
        $this->assertArrayHasKey('upsub-secret', $response->headers);
        $this->assertArrayHasKey('upsub-connection-name', $response->headers);
        $this->assertEquals('app-id', $response->headers['upsub-app-id']);
        $this->assertEquals('secret', $response->headers['upsub-secret']);
        $this->assertEquals('name', $response->headers['upsub-connection-name']);
    }

    /**
     * Should send a message
     */
    public function testShouldSendRawMessage()
    {
        $msg = new Message(
            Message::TEXT,
            "channel",
            ['header-key' => 'header-value'],
            ['payload-key' => 'payload-value']
        );

        $response = $this->client->sendMessage($msg);

        $this->assertEquals($msg->encode(), $response->response);
    }

    /**
     * Should send a text message on a channel
     */
    public function testShouldSendTextMessage()
    {
        $response = $this->client->send('some-channel', 'payload');

        $this->assertEquals(
            Message::text('some-channel', 'payload')->encode(),
            $response->response
        );
    }

    /**
     * Should throw exeption if the UpSub server doesn't response with 200 ok.
     */
    public function testShouldThroughIfUpSubIsGone()
    {
        $client = new Client('http://not-upsub.com', [
            'dependencies' => [
                'curl' => CurlMock::class
            ]
        ]);

        try {
            $response = $client->send('some-channel', 'payload');
        } catch (Exception $error) {
            $this->assertInstanceOf(Exception::class, $error);
        }
    }

    /**
     * Should throw exception if a channel contains spaces.
     */
    public function testShouldThroughIfChannelIncludesSpaces()
    {
        $client = new Client('http://not-upsub.com', [
            'dependencies' => [
                'curl' => CurlMock::class
            ]
        ]);

        try {
            $response = $client->send('some channel', 'payload');
        } catch (\UnexpectedValueException $error) {
            $this->assertInstanceOf(\UnexpectedValueException::class, $error);
        }
    }

    /**
     * Should create and return a new Channel object
     */
    public function testShouldCreateAndReturnChannel()
    {
        $channel = $this->client->channel('channel');

        $this->assertInstanceOf(Channel::class, $channel);
        $this->assertEquals(['channel'], $channel->channels);
    }

    /**
     * Should create and return a new Channel object containing multiple channels
     */
    public function testShouldCreateMultipleChannels()
    {
        $multiChannel = $this->client->channel('channel-1', 'channel-2');
        $this->assertEquals(['channel-1', 'channel-2'], $multiChannel->channels);
    }
}
