<?php

namespace kafka;

use RdKafka\KafkaConsumer;

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


    /**
     *
     * 返回当前kafka连接的主题信息
     *
     * @return \RdKafka\Metadata\Collection|\RdKafka\Metadata\Topic[]|null
     * @throws \RdKafka\Exception
     */

    public function topics()
    {
        if (!self::$consumerInstance instanceof \RdKafka\Consumer && !self::$consumerInstance instanceof KafkaConsumer)
            throw new \Exception('请先创建一个消费者实例');

        $metadata = self::$consumerInstance->getMetadata(true, null, $this->consumeConfig->consumeTimeout);

        if (!$metadata)
            return null;

        return $metadata->getTopics();

    }


    /**
     *
     * 输出debug信息
     *
     * @param $info
     */

    public function info($info)
    {
        if ($this->consumeConfig->debug) {
            $info = self::object2Array($info);

            $str = is_string($info) ? $info : json_encode($info, JSON_UNESCAPED_UNICODE);
            echo $str . PHP_EOL;
        }
    }


    /**
     *
     * 对象或数组对象转普通数组
     *
     * @param $input
     * @return array
     */

    protected static function object2Array($input)
    {
        if (!is_object($input) && !is_array($input))
            return $input;

        is_object($input) && $input = get_object_vars($input);

        return array_map(function ($item) {

            return (is_object($item) || is_array($item)) ? self::object2Array($item) : $item;

        }, $input);
    }


}