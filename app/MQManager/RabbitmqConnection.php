<?php

namespace App\MQManager;

abstract class RabbitmqConnection implements ConnectionInterFace
{
    /**     * 队列名称     */
    const QUEUE_NAME = '';

    /**     * 通道名称     */
    const EXCHANGE_NAME = '';

    /**
     * 通道类型：direct、fanout、topic、x-delayed-message（延迟消息）
     */
    const TYPE_NAME = '';

    /**     * 路由     */
    const ROUTING_KEY_NAME = '';

    /**     * @var Rabbitmq */
    protected $queue;

    /** @var bool 是否不需要回复确认即被认为被消费 */
    protected $noack = true;

    protected $option = [];

    /**
     * 默认配置信息
     * @var string[]
     */
    protected $defaultConfiguration = [
        'host' => '',
        'port' => '',
        'user' => '',
        'password' => '',
        'vhost' => '',
    ];

    protected $params = ['host', 'port', 'user', 'password', 'vhost'];

    public function __construct()
    {
        $this->setConfiguration();

        $this->verify();

        $this->queue = new Rabbitmq(
            $this->defaultConfiguration,
            static::EXCHANGE_NAME,
            static::QUEUE_NAME,
            static::TYPE_NAME,
            static::ROUTING_KEY_NAME,
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
     * 验证配置信息
     */
    final private function verify()
    {
        foreach ($this->defaultConfiguration as $key => $value) {

            if (!in_array($key, $this->params)) {
                throw new \InvalidArgumentException("Invalid key {$key}");
            }

            if (is_null($value) || empty($value)) {
                throw new \InvalidArgumentException("Config key {$key} cannot is null");
            }
        }
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

    /**
     * 自定义回调处理
     *
     * @param $msg
     * @return \Closure
     */
    public function callback($msg): \Closure
    {
        return function () {

        };
    }
}
