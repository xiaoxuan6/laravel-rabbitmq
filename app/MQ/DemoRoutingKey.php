<?php

namespace App\MQ;

use App\MQManager\RabbitmqConnection;

class DemoRoutingKey extends RabbitmqConnection
{
    const QUEUE_NAME = 'queue_key';

    const EXCHANGE_NAME = 'exchange_key';

    const TYPE_NAME = 'direct';

    const ROUTING_KEY_NAME = 'route_key';

    public function setConfiguration()
    {
        $this->defaultConfiguration = [
            'host' => '127.0.0.1',
            'port' => '5672',
            'user' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
        ];
    }

    public function receive($body)
    {
        echo $body;
    }

    public function callback($msg): \Closure
    {
        return function () use ($msg) {
            echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
        };
    }
}
