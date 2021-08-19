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
    protected $noack = false;

    protected $option = [
        'delay' => true, // 开启延迟消费者,
        'prefetch_count' => 10 // 给每个消费者每次分配多少消息
    ];

    public function setConfiguration()
    {
        $this->defaultConfiguration = config('services.rabbitmq');
    }

    public function receive($body)
    {
        $time = Carbon::now()->toTimeString();

        echo 'body：' . $body['body'] . ' 生产时间：' . $body['time'] . ' 消费时间：' . $time . ' 损耗时间：' . Carbon::parse($time)->diffInSeconds($body['time']) . PHP_EOL;
    }

}
