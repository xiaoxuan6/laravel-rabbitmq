<?php

namespace App\Console\Commands;

use App\MQManager\ConfigAttribute;
use App\MQManager\Configuration;
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
        $config = new ConfigAttribute('hyperf', 'laravel', 'fanout', false, [
            'prefetch_count' => 100, // 给每个消费者每次分配多少消息
//            'consumer_tag' => 'amq.ctag-9eRYpBpSWEpeglsixmq0Ew'
        ]);

        $mq = new \App\MQManager\RabbitmqManager($config);
        $mq->pull(function ($body) {

            var_export($body['id'] . PHP_EOL);

            sleep(1);
        });
    }
}
