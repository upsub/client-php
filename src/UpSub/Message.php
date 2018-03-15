<?php

namespace UpSub;

class Message
{
    /**
     * Type of the message
     * @var string
     */
    public $type;

    /**
     * Channel the message should be send on
     * @var string
     */
    public $channel;

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
     * Message type json
     * @var string
     */
    const JSON = 'json';

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
    public function __construct($type, $channel, $headers, $payload)
    {
        $this->type = $type;
        $this->channel = $channel;
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
        if (!is_string($payload)) {
            return static::json($channel, $payload);
        }

        return new Message(static::TEXT, $channel, [], $payload);
    }

    /**
     * Create a new json message
     * @param  string $channel
     * @param  mixed $payload
     * @return Message
     */
    public static function json($channel, $payload)
    {
        return new Message(static::JSON, $channel, [], $payload);
    }

    /**
     * Create a new batch message
     * @param  Message[] $messages
     * @return Message
     */
    public static function batch($messages)
    {
        return new Message(static::BATCH, "", [], $messages);
    }

    /**
     * Encode the message to json
     * @return string encoded message in json
     */
    public function encode()
    {
        $msg = $this->type.' '.$this->channel;
        $payload = $this->payload;

        foreach ($this->headers as $key => $value) {
            $msg .= "\n".$key.": ".$value;
        }

        if (!is_null($payload)) {
            $msg .= "\n\n";
        }

        if (!is_string($payload)) {
            $msg .= json_encode($payload);
        } else {
            $msg .= $payload;
        }

        return $msg;
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
