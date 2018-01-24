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
        $msg = new Message($headers, $payload);

        $this->assertInstanceOf(Message::class, $msg);
        $this->assertEquals((object)$headers, $msg->headers);
        $this->assertEquals($payload, $msg->payload);
    }

    /**
     * Should Have static message types
     */
    public function testShouldHaveStaticMessagetypes()
    {
        $this->assertEquals('text', Message::TEXT);
        $this->assertEquals('batch', Message::BATCH);
    }

    /**
     * Should create a new text message
     */
    public function testShouldCreateTextMessage()
    {
        $msg = Message::text('some-channel', 'payload');
        $expectedHeaders = (object)[
            'upsub-message-type' => 'text',
            'upsub-channel' => 'some-channel'
        ];
        $expectedPayload = 'payload';

        $this->assertEquals($expectedHeaders, $msg->headers);
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

        $expectedHeaders = (object)[
            'upsub-message-type' => 'batch'
        ];
        $expectedPayload = $messages;

        $this->assertEquals($expectedHeaders, $msg->headers);
        $this->assertEquals($expectedPayload, $msg->payload);
    }

    /**
     * Should encode message to JSON string
     */
    public function testShouldEncodeMessageToJSON()
    {
        $headers = ['header-key' => 'header-value'];
        $payload = ['payload-key' => 'payload-value'];

        $msg = new Message($headers, $payload);

        $expectedEncoding = json_encode([
            'headers' => $headers,
            'payload' => json_encode($payload)
        ]);

        $this->assertEquals($expectedEncoding, $msg->encode());
    }

    /**
     * Should encode message if its used in a string context
     */
    public function testShouldEncodeMessageInStringContext()
    {
        $headers = ['header-key' => 'header-value'];
        $payload = ['payload-key' => 'payload-value'];

        $msg = new Message($headers, $payload);

        $expectedEncoding = json_encode([
            'headers' => $headers,
            'payload' => json_encode($payload)
        ]);

        $this->assertEquals($expectedEncoding, "$msg");
    }
}
