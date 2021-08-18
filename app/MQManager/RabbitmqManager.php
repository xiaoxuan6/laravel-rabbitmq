<?php

namespace App\MQManager;

use Illuminate\Support\Arr;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitmqManager
{
    protected $delay;

    /** @var $attribute ConfigAttribute */
    protected $attribute;

    /** @var $connection AMQPStreamConnection */
    protected $connection;

    /** @var $channel AMQPChannel */
    protected $channel;

    public function __construct(ConfigAttribute $attribute)
    {
        $this->connect();
        $this->attribute = $attribute;
    }

    protected function setDelay($delay = 0): RabbitmqManager
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * 生产者
     * @param $body
     * @param int $delay
     */
    public function push($body, int $delay = 0)
    {
        $this->setDelay($delay);

        $channel = $this->channel;
        $attribute = $this->attribute;

        $exchange = $attribute->getExchange();
        $type = $attribute->getType();
        $queue = $attribute->getQueue();

        $this->bind($channel, $exchange, $type, $queue);

        $properties = $attribute->getDurableMessage() ? [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT // 消息持久化
        ] : [];

        if ($delay > 0) {
            $table = new AMQPTable();
            $table->set('x-delay', $delay * 1000);

            $properties = $properties + ['application_headers' => $table];
        }

        $message = new AMQPMessage(json_encode($body, JSON_UNESCAPED_UNICODE), $properties);

        $channel->basic_publish($message, $exchange);
    }

    /**
     * 消费者
     */
    public function pull(\Closure $closure)
    {
        $channel = $this->channel;
        $attribute = $this->attribute;

        $exchange = $attribute->getExchange();
        $type = $attribute->getType();
        $queue = $attribute->getQueue();
        $noAck = $attribute->getNoAck();
        $prefetch_size = $attribute->getPrefetchSize();
        $prefetch_count = $attribute->getPrefetchCount();
        $a_global = $attribute->getGlobal();
        $consumer_tag = $attribute->getConsumerTag();

        $this->bind($channel, $exchange, $type, $queue);

        /**
         * @param int $prefetch_size 服务器传送最大内容量（以八位字节计算），如果没有限制，则为0
         * @param int $prefetch_count 服务器每次传递的最大消息数，如果没有限制，则为0；
         * @param bool $a_global 如果为true,则当前设置将会应用于整个Channel(频道)
         */
        $channel->basic_qos($prefetch_size, $prefetch_count, $a_global);

        /**
         * @param string $queue 消息要取得消息的队列名
         * @param string $consumer_tag 消费者标签
         * @param bool $no_local 这个功能属于AMQP的标准 ,但是rabbitMQ并没有做实现.
         * @param bool $no_ack 收到消息后 ,是否不需要回复确认即被认为被消费
         * @param bool $exclusive 排他消费者 ,即这个队列只能由一个消费者消费.适用于任务不允许进行并发处理的情况下.比如系统对接
         * @param bool $nowait 不返回执行结果 ,但是如果排他开启的话,则必须需要等待结果的,如果两个一起开就会报错
         * @param callable|null $callback 回调函数
         */
        $channel->basic_consume($queue, $consumer_tag, false, $noAck, false, false, function ($msg) use ($noAck, $closure) {

            try {

                $body = json_decode($msg->body, true);

                $closure($body);

                if (!$noAck) {
                    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                }

            } catch (\Exception $exception) {

                die($exception->getMessage());

            }

        });

        while (true) {

            $channel->wait();

        }
    }

    /**
     * 交换机绑定队列
     * @param AMQPChannel $channel
     * @param $exchange
     * @param $type
     * @param $queue
     */
    public function bind(AMQPChannel $channel, $exchange, $type, $queue)
    {
        $attribute = $this->attribute;

        $durable = $attribute->getDurable();

        // 延迟队列
        $queueArguments = $arguments = [];
        if ($this->delay > 0 || $attribute->getDelay()) {
            $arguments = new AMQPTable(['x-delayed-type' => $type]);
            $type = 'x-delayed-message';
            $queueArguments = new AMQPTable(['x-dead-letter-exchange' => 'delayed']);
        }

        /**
         * 声明初始化交换机（
         *      direct【精准推送】、
         *      fanout【广播。推送到绑定到此交换机下的所有队列】、
         *      topic【组播。比如上面我绑定的关键字是sms_send，那么他可以推送到*.sms_send的所有队列】
         * ）
         * @param string $exchange 交换机名称
         * @param string $type 交换机类型
         * @param bool $passive 是否检测同名队列
         * @param bool $durable 是否开启队列持久化
         * @param bool $auto_delete 通道关闭后是否删除队列
         */
        $channel->exchange_declare($exchange, $type, false, $durable, false, false, false, $arguments);

        /**
         * 声明一个队列
         * @param bool $queue 队列名称
         * @param bool $passive 是否检测同名队列
         * @param bool $durable 是否开启队列持久化
         * @param bool $exclusive 队列是否可以被其他队列访问
         * @param bool $auto_delete 通道关闭后是否删除队列
         */
        $channel->queue_declare($queue, false, $durable, false, false, false, $queueArguments);

        /**
         * 将队列与交换机进行绑定
         */
        $channel->queue_bind($queue, $exchange);
    }

    /**
     * 链接 amqp
     */
    private function connect()
    {
        try {

            $config = config('services.rabbitmq');
            $host = Arr::get($config, 'host');
            $port = Arr::get($config, 'port');
            $user = Arr::get($config, 'user');
            $password = Arr::get($config, 'password');
            $vhost = Arr::get($config, 'vhost');

            $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);

            $channel = $connection->channel();

            $this->connection = $connection;
            $this->channel = $channel;

            register_shutdown_function(function () use ($connection, $channel) {

                try {

                    $channel->close();
                    $connection->close();

                } catch (\Exception $exception) {

                }

            });

        } catch (\Exception $exception) {

            die($exception->getMessage());

        }
    }
}
