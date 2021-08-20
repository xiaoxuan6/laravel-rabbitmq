<?php

namespace App\Http\Controllers;

use App\MQ\DemoMq;
use App\MQ\DemoRoutingKey;
use App\MQManager\Rabbitmq;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function product()
    {
//        $mq = new Rabbitmq('demo_product', 'demo_product_queue', 'direct');
//        $mq->push(['name' => 'eto', 'time' => Carbon::now()->toDateTimeString()], 1 * 60);

        $mq = new DemoMq();
        $mq->send(['body' => 'hello world', 'time' => Carbon::now()->toTimeString()], 0);

        $mq = new DemoRoutingKey();
        $mq->send('2323232', 0);
        dd('ok');
    }
}
