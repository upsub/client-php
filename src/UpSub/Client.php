<?php
namespace UpSub;

use \Curl\Curl;
use UpSub\Channel;
use UpSub\Message;

class Client
{
    /**
     * Host
     * @var string
     */
    private $host;

    /**
     * Options
     * @var stdClass
     */
    private $options;

    /**
     * Create a new instance of the client
     * @param  string      $host
     * @param  string      $options
     * @return Client
     */
    public function __construct($host, $options = [])
    {
        $this->host = $host;
        $this->options = new \stdClass;

        $this->setDefaultOptions($options);
    }

    /**
     * Setup default option values if nothing is specified
     * @method setupOptions
     * @param  array       $options
     * @return void
     */
    private function setDefaultOptions($options)
    {
        $options = (object)$options;

        $this->options->name = isset($options->name)
            ? $options->name
            : null;

        $this->options->appID = isset($options->appID)
            ? $options->appID
            : null;

        $this->options->secret = isset($options->secret)
            ? $options->secret
            : null;

        $this->options->timeout = isset($options->timeout)
            ? $options->timeout
            : 5;

        $this->options->dependencies = (object)[];
        $this->options->dependencies->Curl = isset($options->dependencies['curl'])
            ? $options->dependencies['curl']
            : Curl::class;
    }

    /**
     * create new curl request
     * @return Curl
     */
    private function createRequest()
    {
        $headers = [];

        if (!is_null($this->options->appID)) {
            $headers['upsub-app-id'] = $this->options->appID;
        }

        if (!is_null($this->options->secret)) {
            $headers['upsub-secret'] = $this->options->secret;
        }

        if (!is_null($this->options->name)) {
            $headers['upsub-connection-name'] = $this->options->name;
        }

        $request = new $this->options->dependencies->Curl;
        $request->setTimeout = $this->options->timeout;
        $request->setHeaders($headers);

        return $request;
    }

    /**
     * Send message to the upsub dispatcher
     * @param  Message      $message
     * @return Curl
     */
    public function sendMessage($message)
    {
        $request = $this->createRequest();
        $request->post($this->host.'/v1/send', $message->encode());

        if ($request->error) {
            throw new \Exception(
                "UpSub response error $request->errorCode: $request->errorMessage"
            );
        }

        return $request;
    }

    /**
     * Send message on a specific channel
     * @param  string        $channel
     * @param  mixed         $payload
     * @return Curl
     */
    public function send($channel, $payload)
    {
        if (strpos($channel, ' ') !== false) {
            throw new \UnexpectedValueException("Channel can't contain spaces");
        }

        return $this->sendMessage(Message::text($channel, $payload));
    }

    /**
     * Create a new Channel object
     * @param  string    $channels
     * @return Channel
     */
    public function channel($channels)
    {
        if (!is_array($channels)) {
            $channels = func_get_args();
        }

        return new Channel($channels, $this);
    }
}
