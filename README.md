# php client

[![Latest Stable Version](https://poser.pugx.org/upsub/client/version)](https://packagist.org/packages/upsub/client)
[![Build Status](https://travis-ci.org/upsub/client-php.svg?branch=master)](https://travis-ci.org/upsub/client-php)

This is the php client for UpSub, its communicating to the [Dispatcher](https://github.com/upsub/dispatcher) via the http
protocol. This client is write only and can therefore not listen for message from the Dispatcher.
> Only clients with subscription support can listen for message, see the [JavaScript client](https://github.com/upsub/client-js).

## Install
The client is published to packagist and can be installed with [`composer`](https://getcomposer.org/).
```sh
composer require upsub/client
```

## Usage
```php
<?php
use UpSub\Client;

$client = new Client('http://localhost:4400', [
  // options
]);
```

#### Options
- `name: string`: Name the connection, useful for debugging.
- `appID: string`: App identifier for the dispatcher authentication.
- `secret: string`: Secret key for authentication.
- `timeout: int`: If the http request exceeds the timeout then terminate the request, default is `5` seconds.

#### Create a channel
The best way to structure eventstreams is by scoping it in different channels.
```php
<?php
// Single channel
$channel = $client->channel('channe');

// Multiple channels
$multiChannel = $client->channel('channel-1', 'channel-2');
```

#### Send message
Send message to the UpSub [Dispatcher](https://github.com/upsub/dispatcher).
```php
<?php
// Send message from the channel
$channel->send('event', [ 'key' => 'value' ]);

// Send message directly from client
$client->send('event', 'my message...');
```

## License
Released under the [MIT License](https://github.com/upsub/client-php/blob/master/LICENSE)
