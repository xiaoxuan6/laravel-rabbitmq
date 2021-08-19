<?php

namespace App\MQManager;

class RabbitmqConnection implements ConnectionInterFace
{
    /**     * 队列名称     */
    const QUEUE_NAME = '';

    /**     * 通道名称     */
    const EXCHANGE_NAME = '';

    /**     * 通道类型     */
    const TYPE_NAME = '';

    /**     * @var Rabbitmq */
    protected $queue;

    /** @var bool 是否不需要回复确认即被认为被消费 */
    protected $noack = false;

    protected $option = [];

    public function __construct()
    {
        $this->queue = new Rabbitmq(
            static::EXCHANGE_NAME,
            static::QUEUE_NAME,
            static::TYPE_NAME,
            $this->noack,
            $this->option
        );
    }

    /**
     * 消费者
     *
     * @return mixed|void
     */
    public function run()
    {
        $this->queue->setListener(new static())->pull();
    }

    /**
     * 生产者
     *
     * @param string|array $body 发送消息内容
     * @param int $delay 延迟时间，秒数
     * @return mixed|void
     */
    public function send($body, int $delay = 0)
    {
        $this->queue->push($body, $delay);
    }

    /**
     * 消费者处理器
     *
     * @param $body
     * @return mixed|void
     */
    public function receive($body)
    {
        // TODO: Implement receive() method.
    }
}
