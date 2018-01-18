<?php
namespace UpSub;

use \Curl\Curl;
use UpSub\Channel;

class Client
{
    /**
     * App key from the UpSub Server
     * @var string
     */
    protected $appKey;

    /**
     * Secret key from the UpSub Server
     * @var string
     */
    protected $secretKey;

    /**
     * Options
     * @var stdClass
     */
    public $options;

    /**
     * Setup the Client
     * @method __constructor
     * @param  string      $appKey
     * @param  string      $secretKey
     * @return Client
     */
    public function __construct($appKey, $secretKey, $options = [])
    {
        $this->appKey = $appKey;
        $this->secretKey = $secretKey;
        $this->options = new \stdClass;

        $this->setupOptions($options);
    }

    /**
     * Setup default option values if nothing is specified
     * @method setupOptions
     * @param  array       $options
     * @return void
     */
    protected function setupOptions($options)
    {
        $options = (object)$options;

        $this->options->name = isset($options->name)
            ? $options->name
            : '';

        $this->options->host = isset($options->host)
            ? $options->host
            : 'https://upsub.uptime.dk';

        $this->options->timeout = isset($options->timeout)
            ? $options->timeout / 1000
            : 5;

        $this->options->dependencies = (object)[];
        $this->options->dependencies->curl = isset($options->dependencies['curl'])
            ? $options->dependencies['curl']
            : Curl::class;
    }

    /**
     * create new curl request
     * @method setupCurl
     * @return Curl
     */
    protected function createRequest()
    {
        $request = new $this->options->dependencies->curl;
        $request->setTimeout = $this->options->timeout;
        $request->setHeaders([
            'APP-KEY' => $this->appKey,
            'SECRET-KEY' => $this->secretKey
        ]);

        return $request;
    }

    /**
     * Send event on a specific channel to the UpSub API
     * @method sendOnChannel
     * @param  string        $channel
     * @param  string        $event
     * @param  array         $data
     * @param  string        $type
     * @return Curl
     */
    public function sendOnChannel($channel, $event, $data, $type = 'message')
    {
        $request = $this->createRequest();
        $request->post($this->options->host.'/api/1.0/send-event', [
            'type' => $type,
            'client' => $this->options->name,
            'channel' => $channel,
            'event' => $event,
            'message' => json_encode($data)
        ]);

        if ($request->error) {
            throw new \Exception('UpSub response error '.$request->errorCode.': '.$request->errorMessage);
        }

        return $request;
    }

    /**
     * Send event to the UpSub API
     * @method send
     * @param string        $event
     * @param array         $data
     * @return Curl
     */
    public function send($event, $data)
    {
        return $this->sendOnChannel('', $event, $data);
    }

    /**
     * Subscribe to channels
     * @method subscribe
     * @param  string    $channels
     * @return Channel
     */
    public function subscribe($channels)
    {
        if (!is_array($channels)) {
            $channels = func_get_args();
        }

        return new Channel($this, $channels);
    }
}
