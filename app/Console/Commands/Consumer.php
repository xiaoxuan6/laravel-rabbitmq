<?php

namespace App\Console\Commands;

use App\MQManager\Configuration;
use App\MQManager\Rabbitmq;
use Illuminate\Console\Command;

class Consumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amqp:consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 手动回复 ack 确认消费成功
        $mq = new Rabbitmq('hyperf', 'laravel', 'fanout', true, [
            'prefetch_count' => 100, // 给每个消费者每次分配多少消息
//            'consumer_tag' => 'amq.ctag-9eRYpBpSWEpeglsixmq0Ew'
        ]);

        $mq->pull(function ($body) {

            var_export($body['id'] . PHP_EOL);

            sleep(1);
        });
    }
}
