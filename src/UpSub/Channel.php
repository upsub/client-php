<?php
namespace UpSub;

class Channel
{
    /**
     * UpSub Client instance
     * @var Client
     */
    protected $client;

    /**
     * Subscribed channels
     * @var array
     */
    public $channels;

    /**
     * Serup the channel instance
     * @method __construct
     * @param  Client     $client
     * @param  array      $channels
     */
    public function __construct($client, $channels)
    {
        $this->client = $client;
        $this->channels = $channels;
    }

    /**
     * Send event on channels
     * @method send
     * @param  String $event
     * @param  array  $data
     * @return Curl   returns an array of the curl responses
     */
    public function send($event, $data)
    {
        $responses = [];

        foreach ($this->channels as $channel) {
            $responses[] = $this->client->sendOnChannel($channel, $event, $data);
        }

        return $responses;
    }
}
