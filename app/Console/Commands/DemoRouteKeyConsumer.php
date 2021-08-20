<?php

namespace App\Console\Commands;

use App\MQ\DemoRoutingKey;
use Illuminate\Console\Command;

class DemoRouteKeyConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo::key:consumer';

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
        $mq = new DemoRoutingKey();
        $mq->run();
    }
}
