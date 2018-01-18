<?php

use UpSub\Client;
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
     * @method __construct
     */
    public function __construct()
    {
        $this->client = new Client('APP_KEY', 'SECRET_KEY', [
            'name' => 'client-name',
            'dependencies' => [
                'curl' => CurlMock::class
            ]
        ]);
    }

    /**
     * Should test that the new Client is an instance of UpSub/Client
     * @method testShouldNewClient
     * @return void
     */
    public function testShouldBeInstanceOfUpSubClient()
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    /**
     * Should set APP_KEY and SECRET_KEY as HTTP Headers
     * @method testShouldSetHTTPHeaders
     * @return void
     */
    public function testShouldSetHTTPHeaders()
    {
        $curl = $this->client->send('event', [
            'some' => 'data'
        ]);

        $this->assertContains('APP_KEY', $curl->headers);
        $this->assertContains('SECRET_KEY', $curl->headers);
    }

    /**
     * Should send event in the global event scope
     * @method testShouldSendEventInGlobalScope
     * @return void
     */
    public function testShouldSendEventInGlobalScope()
    {
        $curl = $this->client->send('event', [
            'some' => 'data'
        ]);

        $this->assertEquals([
            'type' => 'message',
            'client' => 'client-name',
            'channel' => '',
            'event' => 'event',
            'message' => '{"some":"data"}'
        ], $curl->response);
    }

    /**
     * Should send event on a specific channel
     * @method testShouldSendEventOnSpecificChannel
     * @return void
     */
    public function testShouldSendEventOnSpecificChannel()
    {
        $curl = $this->client->sendOnChannel('channel', 'event', [
            'some' => 'data'
        ]);

        $this->assertEquals([
            'type' => 'message',
            'client' => 'client-name',
            'channel' => 'channel',
            'event' => 'event',
            'message' => '{"some":"data"}'
        ], $curl->response);
    }

    /**
     * Should throw an exception while sending event, if the UpSub API isn't
     * responding with 200 OK
     * @method testShouldThrowExceptionWhileSendingEvent
     * @return void
     */
    public function testShouldThrowExceptionWhileSendingEvent()
    {
        $this->client->options->host = 'http://should.fail';

        try {
            $curl = $this->client->send('event', [
                'some' => 'data'
            ]);
        } catch (Exception $error) {
            $this->assertInstanceOf(Exception::class, $error);
        }
    }

    /**
     * Should subscribe to channels and return the channel object
     * @method testShouldSubscribeToChannels
     * @return void
     */
    public function testShouldSubscribeToChannels()
    {
        $channel = $this->client->subscribe('channel');

        $this->assertInstanceOf('UpSub\Channel', $channel);
        $this->assertEquals(['channel'], $channel->channels);
    }

    /**
     * Should send event on a specific channel via the channel object
     * @method testShouldSendEventOnChannels
     * @return void
     */
    public function testShouldSendEventOnSingleChannel()
    {
        $channel = $this->client->subscribe('my-channel');

        $curls = $channel->send('event', ['some' => 'data']);

        $this->assertEquals([
            'type' => 'message',
            'client' => 'client-name',
            'channel' => 'my-channel',
            'event' => 'event',
            'message' => '{"some":"data"}'
        ], $curls[0]->response);
    }

    /**
     * Should send event on multiple channels at once
     * @method testShouldSendEventOnMultipleChannels
     * @return void
     */
    public function testShouldSendEventOnMultipleChannels()
    {
        $channel = $this->client->subscribe('channel-1', 'channel-2');

        $curls = $channel->send('event', ['some' => 'data']);

        foreach ($curls as $key => $curl) {
            $this->assertEquals([
                'type' => 'message',
                'client' => 'client-name',
                'channel' => 'channel-'.($key+1),
                'event' => 'event',
                'message' => '{"some":"data"}'
            ], $curl->response);
        }

        $channel = $this->client->subscribe(['channel-1', 'channel-2']);

        $curls = $channel->send('event', ['some' => 'data']);

        foreach ($curls as $key => $curl) {
            $this->assertEquals([
                'type' => 'message',
                'client' => 'client-name',
                'channel' => 'channel-'.($key+1),
                'event' => 'event',
                'message' => '{"some":"data"}'
            ], $curl->response);
        }
    }
}
