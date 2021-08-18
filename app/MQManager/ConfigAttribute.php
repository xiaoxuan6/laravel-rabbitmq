<?php

namespace App\MQManager;

class ConfigAttribute
{
    /** 交换机类型 @var */
    protected $type;

    /** 队列名称 @var */
    protected $queue;

    /** 交换机名称 @var */
    protected $exchange;

    /** @var bool 是否不需要回复确认即被认为被消费 */
    protected $noAck;

    /** @var bool 是否开启队列持久化; true：服务器重启会保留下来Exchange */
    protected $durable = false;

    /** @var int 最大 unacked 消息的字节数 */
    protected $prefetch_size = 0;

    /** @var int 最大 unacked 消息的条数; 只有在no_ask=false的情况下生效 */
    protected $prefetch_count = 0;

    /** @var bool false=限制单个消费者; true=限制整个信道 */
    protected $global = false;

    /** @var string 消费者标签 */
    protected $consumer_tag = '';

    public function __construct($exchange, $queue, $type, $noAck = true, $option = [])
    {
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->type = $type;
        $this->noAck = $noAck;

        foreach ($option as $key => $val) {

            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }

        }
    }

    public function getType()
    {
        return $this->type;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function getExchange()
    {
        return $this->exchange;
    }

    public function getNoAck()
    {
        return $this->noAck;
    }

    public function getDurable(): bool
    {
        return $this->durable;
    }

    public function getPrefetchSize(): int
    {
        return $this->prefetch_size;
    }

    public function getPrefetchCount(): int
    {
        return $this->prefetch_count;
    }

    public function getGlobal(): int
    {
        return $this->global;
    }

    public function getConsumerTag(): string
    {
        return $this->consumer_tag;
    }
}
