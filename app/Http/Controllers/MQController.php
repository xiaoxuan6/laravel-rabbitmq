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
        $manage = new Rabbitmq('hyperf_delay', 'laravel_delay', 'direct');

        for ($i = 0; $i < 100; $i++) {

            $manage->push(['id' => 'test' . $i, 'time' => Carbon::now()->toDateTimeString()], $i);
        }

        dd('ok');
    }
}
