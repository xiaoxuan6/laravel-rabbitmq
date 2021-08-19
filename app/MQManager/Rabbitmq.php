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

    public function __construct($configuration, $exchange, $queue, $type, $noAck = true, $option = [])
    {
        $this->init($configuration, $exchange, $queue, $type, $noAck, $option);
    }

    /**
     * @param $configuration
     * @param $exchange
     * @param $queue
     * @param $type
     * @param $noAck
     * @param $option
     */
    private function init($configuration, $exchange, $queue, $type, $noAck, $option)
    {
        $this->mq = new RabbitmqManager($configuration, new ConfigAttribute($exchange, $queue, $type, $noAck, $option));
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

        $this->mq->pull(function ($body) use ($listener) {

            $listener->receive($body);

        });
    }

    public function setListener($listener): Rabbitmq
    {
        $this->listener = $listener;
        return $this;
    }

}
