<?php

namespace App\MQManager;

abstract class RabbitmqConnection implements ConnectionInterFace
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

    protected $defaultConfiguration = [
        'host' => '',
        'port' => '',
        'user' => '',
        'password' => '',
        'vhost' => '',
    ];

    public function __construct()
    {
        $this->setConfiguration();

        $this->queue = new Rabbitmq(
            $this->defaultConfiguration,
            static::EXCHANGE_NAME,
            static::QUEUE_NAME,
            static::TYPE_NAME,
            $this->noack,
            $this->option
        );
    }

    /**
     * 设置账号密码
     * @return mixed
     */
    abstract public function setConfiguration();

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
     * @param int $delay 延迟时间，秒数(这里延迟时间是针对交换机把消息传给队列的时间，实际延时时间从消息推送到消费之间的时间)
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
    abstract public function receive($body);
}
