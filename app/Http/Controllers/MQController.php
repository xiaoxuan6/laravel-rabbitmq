<?php

namespace App\Http\Controllers;

use App\MQManager\Rabbitmq;
use Carbon\Carbon;

class MQController extends Controller
{
    public function push()
    {
        $manage = new Rabbitmq('hyperf', 'laravel', 'fanout');

        for ($i = 0; $i < 10000; $i++) {

            $manage->push(['id' => 'test' . $i]);
        }

        dd('ok');
    }

    public function pushSleep()
    {
        $manage = new Rabbitmq('hyperf_delay_1', 'laravel_delay_1', 'direct');

        for ($i = 1; $i <= 100; $i++) {

            $manage->push(['id' => 'test' . $i, 'time' => Carbon::now()->toDateTimeString()], $i + 10);
        }

        dd('ok');
    }
}
