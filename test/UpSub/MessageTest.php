<?php

use UpSub\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /**
     * Should create a new message
     */
    public function testShouldCreateMessage()
    {
        $headers = ['header-key' => 'header-value'];
        $payload = ['payload-key' => 'payload-value'];
        $msg = new Message(Message::TEXT, 'channel', $headers, $payload);

        $this->assertInstanceOf(Message::class, $msg);
        $this->assertEquals('text', $msg->type);
        $this->assertEquals('channel', $msg->channel);
        $this->assertEquals((object)$headers, $msg->headers);
        $this->assertEquals((object)$headers, $msg->headers);
        $this->assertEquals($payload, $msg->payload);
    }

    /**
     * Should Have static message types
     */
    public function testShouldHaveStaticMessagetypes()
    {
        $this->assertEquals('text', Message::TEXT);
        $this->assertEquals('json', Message::JSON);
        $this->assertEquals('batch', Message::BATCH);
    }

    /**
     * Should create a new text message
     */
    public function testShouldCreateTextMessage()
    {
        $msg = Message::text('some-channel', 'payload');
        $expectedPayload = 'payload';

        $this->assertEquals('text', $msg->type);
        $this->assertEquals('some-channel', $msg->channel);
        $this->assertEquals((object)[], $msg->headers);
        $this->assertEquals($expectedPayload, $msg->payload);
    }

    /**
     * Should create a new json message
     */
    public function testShouldCreateJSONsMessage()
    {
        $msg = Message::json('some-channel', [ 'key' => 'value' ]);
        $expectedPayload = ['key' => value];

        $this->assertEquals('json', $msg->type);
        $this->assertEquals('some-channel', $msg->channel);
        $this->assertEquals((object)[], $msg->headers);
        $this->assertEquals($expectedPayload, $msg->payload);
    }

    /**
     * Should create a new batch message
     */
    public function testShouldCreateBatchMessage()
    {
        $messages = [
            Message::text('channel-1', 'data'),
            Message::text('channel-2', 'data')
        ];

        $msg = Message::batch($messages);
        $expectedPayload = $messages;

        $this->assertEquals("batch", $msg->type);
        $this->assertEquals((object)[], $msg->headers);
        $this->assertEquals($expectedPayload, $msg->payload);
    }

    /**
     * Should encode message to JSON string
     */
    public function testShouldEncodeMessageToJSON()
    {
        $headers = ['header-key' => 'header-value'];
        $payload = ['payload-key' => 'payload-value'];

        $msg = new Message(
            Message::JSON,
            "channel",
            $headers,
            $payload
        );

        $expectedEncoding = "json channel\nheader-key: header-value\n\n{\"payload-key\":\"payload-value\"}";

        $this->assertEquals($expectedEncoding, $msg->encode());
    }

    /**
     * Should encode message if its used in a string context
     */
    public function testShouldEncodeMessageInStringContext()
    {
        $headers = ['header-key' => 'header-value'];
        $payload = ['payload-key' => 'payload-value'];

        $msg = new Message(
            Message::TEXT,
            "channel",
            $headers,
            $payload
        );

        $expectedEncoding = "text channel\nheader-key: header-value\n\n{\"payload-key\":\"payload-value\"}";

        $this->assertEquals($expectedEncoding, "$msg");
    }
}
