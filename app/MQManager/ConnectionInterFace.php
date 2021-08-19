<?php

namespace App\MQManager;

interface ConnectionInterFace
{
    /**
     * 执行消费者
     * @return mixed
     */
    public function run();

    /**
     * 发送生产者数据
     * @param $body
     * @return mixed
     */
    public function send($body);
}
