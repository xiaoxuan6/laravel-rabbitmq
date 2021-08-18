<?php

namespace App\MQManager;

class Rabbitmq
{
    /**
     * @var $mq RabbitmqManager
     */
    protected $mq;

    public function __construct($exchange, $queue, $type, $noAck = true, $option = [])
    {
        $this->init($exchange, $queue, $type, $noAck, $option);
    }

    /**
     * @param $exchange
     * @param $queue
     * @param $type
     * @param $noAck
     * @param $option
     */
    private function init($exchange, $queue, $type, $noAck, $option)
    {
        $this->mq = new RabbitmqManager(new ConfigAttribute($exchange, $queue, $type, $noAck, $option));
    }

    /**
     * @param $body
     */
    public function push($body, $delay = 0)
    {
        $this->mq->push($body, $delay);
    }

    /**
     * @param \Closure $closure
     */
    public function pull(\Closure $closure)
    {
        $this->mq->pull($closure);
    }
}
