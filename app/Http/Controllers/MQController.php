<?php

namespace App\Http\Controllers;

use App\MQManager\Rabbitmq;

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
}
