<?php

namespace App\Http\Controllers;

use App\MQManager\ConfigAttribute;
use App\MQManager\RabbitmqManager;

class MQController extends Controller
{
    public function push()
    {
        $manage = new RabbitmqManager(new ConfigAttribute('hyperf', 'laravel', 'fanout'));

        for ($i = 0; $i < 100; $i++) {

            $manage->push(['id' => 'test' . $i]);
        }

        dd('ok');
    }
}
