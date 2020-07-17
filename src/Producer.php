<?php

namespace kafka;

use RdKafka\Conf;
use RdKafka\TopicConf;

class Producer
{
    /** @var \RdKafka\Producer */
    static $productInstance = null;

    /** @var ProduceConfig $produceConfig */
    protected $produceConfig;


    public function __construct(ProduceConfig $produceConfig)
    {
        $this->produceConfig = $produceConfig;
    }


    /**
     *
     * 生产者
     *
     * @param string $topicName
     * @param array $message
     */

    public function producer($topicName, $message, $partition = RD_KAFKA_PARTITION_UA)
    {
        if (!self::$productInstance instanceof \RdKafka\Producer)
            $this->getInstance();

        $topicConf = new TopicConf();

        $topicConf->set("request.required.acks", $this->produceConfig->requestRequiredMethod);

        $produceTopic = self::$productInstance->newTopic($topicName, $topicConf);

        $produceTopic->produce($partition, 0, $message);

        /*while (self::$productInstance->getOutQLen() > 0) {
            self::$productInstance->poll(2 * 1000);
        }*/

    }


    /**
     * 获取生产者实例
     */

    private function getInstance()
    {

        $conf = new Conf();
        $conf->set('metadata.broker.list', $this->produceConfig->brokers);

        self::$productInstance = new \RdKafka\Producer($conf);
        self::$productInstance->setLogLevel($this->produceConfig->logLevel);

    }

}