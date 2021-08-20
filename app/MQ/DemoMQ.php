<?php

namespace App\MQ;

use App\MQManager\RabbitmqConnection;
use Carbon\Carbon;

class DemoMQ extends RabbitmqConnection
{
    const QUEUE_NAME = 'test_demo_queue';

    const EXCHANGE_NAME = 'test_demo_exchange';

    const TYPE_NAME = 'direct';

    /** @var bool 是否不需要回复确认即被认为被消费 */
    protected $noack = true;

    protected $option = [
        'delay' => false, // 开启延迟消费者, push 消息到队里时如果没有增加延迟时间，不要开启该属性
        'prefetch_count' => 10 // 给每个消费者每次分配多少消息，只有开启上面的 noack 该属性才会生效
    ];

    public function setConfiguration()
    {
//        $this->defaultConfiguration = config('services.rabbitmq');
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
        $time = Carbon::now()->toTimeString();

        echo 'body：' . $body['body'] . ' 生产时间：' . $body['time'] . ' 消费时间：' . $time . ' 损耗时间：' . Carbon::parse($time)->diffInSeconds($body['time']) . PHP_EOL;
    }

    public function callback($msg): \Closure
    {
        return function () use ($msg) {
            echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
        };
    }

}
