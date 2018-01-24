<?php

namespace UpSub;

class Message
{
    /**
     * Message headers
     * @var array
     */
    public $headers;

    /**
     * Message payload
     * @var mixed
     */
    public $payload;

    /**
     * Message type text
     * @var string
     */
    const TEXT = 'text';

    /**
     * Message type batch
     * @var [type]
     */
    const BATCH = 'batch';

    /**
     * Create a new Message instance
     * @param array $headers
     * @param mixed $payload
     */
    public function __construct($headers, $payload)
    {
        $this->headers = (object)$headers;
        $this->payload = $payload;
    }

    /**
     * Create a new text message
     * @param  string $channel
     * @param  mixed $payload
     * @return Message
     */
    public static function text($channel, $payload)
    {
        $headers = [
            'upsub-message-type' => static::TEXT,
            'upsub-channel' => $channel
        ];

        return new Message($headers, $payload);
    }

    /**
     * Create a new batch message
     * @param  Message[] $messages
     * @return Message
     */
    public static function batch($messages)
    {
        $headers = [
            'upsub-message-type' => static::BATCH
        ];

        return new Message($headers, $messages);
    }

    /**
     * Encode the message to json
     * @return string encoded message in json
     */
    public function encode()
    {
        return json_encode([
            'headers' => $this->headers,
            'payload' => json_encode($this->payload)
        ]);
    }

    /**
     * If messaage is used in a string context, then encode.
     * @return string encoded message
     */
    public function __toString()
    {
        return $this->encode();
    }
}
