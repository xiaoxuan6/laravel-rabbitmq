<?php

namespace App\MQManager;

class Rabbitmq
{
    /**
     * @var $mq RabbitmqManager
     */
    protected $mq;

    /**
     * @var $listener ConnectionInterFace
     */
    private $listener;

    public function __construct($configuration, $exchange, $queue, $type, $routing_key = '', $noAck = true, $option = [])
    {
        $this->init($configuration, $exchange, $queue, $type, $routing_key, $noAck, $option);
    }

    /**
     * @param $configuration
     * @param $exchange
     * @param $queue
     * @param $type
     * @param $routing_key
     * @param $noAck
     * @param $option
     */
    private function init($configuration, $exchange, $queue, $type, $routing_key, $noAck, $option)
    {
        $this->mq = new RabbitmqManager($configuration, new ConfigAttribute($exchange, $queue, $type, $routing_key, $noAck, $option));
    }

    /**
     * @param $body
     * @param int $delay
     */
    public function push($body, int $delay = 0)
    {
        $this->mq->push($body, $delay);
    }

    public function pull()
    {
        $listener = $this->listener;

        $this->mq->pull(function ($body, $msg) use ($listener) {

            $listener->receive($body);

            $listener->callback($msg)();

        });
    }

    public function setListener($listener): Rabbitmq
    {
        $this->listener = $listener;
        return $this;
    }

}
