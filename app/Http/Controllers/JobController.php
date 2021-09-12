<?php

namespace App\Http\Controllers;

use App\Jobs\DemoJob;
use App\Jobs\TestJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    public function index()
    {
        $chain = new TestJob(['data' => 'eto']);
        DemoJob::withChain([$chain])->dispatch(['name' => time()]);

        dd(__NAMESPACE__);
    }
}
