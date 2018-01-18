<?php

class CurlMock
{
    public $timeout;
    public $headers;
    public $response;
    public $error;
    public $errorCode = 500;
    public $errorMessage = 'Should fail!';

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function post($url, $data)
    {
        if ($url == 'https://upsub.uptime.dk/api/1.0/send-event') {
            $this->error = false;
            $this->response = $data;
            return $this;
        } else {
            $this->error = true;
            return $this;
        }
    }
}
