<?php

namespace App\Console\Commands;

use App\MQManager\Rabbitmq;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DemoConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:consumer';

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
        $mq = new Rabbitmq('demo_product', 'demo_product_queue', 'direct', false, [
            'delay' => true
        ]);

        $mq->pull(function ($body) {

            $start_time = $body['time'];
            $end_time = Carbon::now()->toDateTimeString();
            echo '发送时间：' . $start_time . ' Name：' . $body['name'] . ' 消费时间：' . $end_time . ' 延迟：' . Carbon::parse($end_time)->diffInSeconds($start_time) . PHP_EOL;

            dd('消费成功，但未删除队列可以重复消费');
        });
    }
}
