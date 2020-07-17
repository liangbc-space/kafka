<?php

namespace kafka;

abstract class Consumer
{

    /** @var \RdKafka\KafkaConsumer|\RdKafka\Consumer|null $consumerInstance */
    protected static $consumerInstance = null;


    /** @var int    自动提交过期时间，超过时间后将自动提交    毫秒 */
    public $autoCommitIntervalMs = 10;

    /** @var ConsumeConfig $consumeConfig */
    protected $consumeConfig;


    /**
     * Consumer constructor.
     * @param ConsumeConfig $consumeConfig
     */
    public function __construct(ConsumeConfig $consumeConfig)
    {
        $this->consumeConfig = $consumeConfig;
    }


    /**
     * @param $groupId
     * @return mixed
     */
    abstract function getInstance($groupId);


}