<?php
namespace UpSub;

class Channel
{
    /**
     * UpSub Client instance
     * @var Client
     */
    private $client;

    /**
     * Subscribed channels
     * @var array
     */
    public $channels;

    /**
     * Serup the channel instance
     * @param  Client     $client
     * @param  array      $channels
     */
    public function __construct($channels, $client)
    {
        $this->channels = $channels;
        $this->client = $client;
    }

    /**
     * Send a message on the specified channels
     * @param  string $channel
     * @param  mixed  $payload
     * @return Curl
     */
    public function send($channel, $payload)
    {
        if (count($this->channels) == 1) {
            return $this->client->send(
                $this->channels[0].'/'.$channel,
                $payload
            );
        }

        $messages = [];

        foreach ($this->channels as $prefix) {
            $messages[] = Message::text(
                $prefix.'/'.$channel,
                $payload
            );
        }

        return $this->client->sendMessage(Message::batch($messages));
    }
}
