<?php

namespace kafka;


use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\TopicConf;

class LowConsumer extends Consumer implements ConsumerInterface
{


    public function getInstance($groupId)
    {

        $conf = new Conf();

        $conf->set('group.id', $groupId);

        $conf->set('metadata.broker.list', $this->consumeConfig->brokers);

        self::$consumerInstance = new \RdKafka\Consumer($conf);

        self::$consumerInstance->setLogLevel($this->consumeConfig->logLevel);

        return self::$consumerInstance;
    }


    /**
     *
     * 低级消费者    多主题和多分区支持
     *
     * @param array $topics ['topicName1' =>  ['partition1', 'partition2'], 'topicName2' =>  ['partition1', 'partition2']]
     * @param callable $callback function($message, $kafkaConsumer){}
     * @throws \Exception
     */

    public function consumer(array $topics, callable $callback)
    {
        if (!self::$consumerInstance instanceof \RdKafka\Consumer)
            throw new \Exception('请先创建一个消费者实例');

        $topicConf = new TopicConf();
        $topicConf->set("auto.commit.interval.ms", $this->autoCommitIntervalMs);
        $topicConf->set("offset.store.method", $this->consumeConfig->offsetStoreMethod);

        $queue = self::$consumerInstance->newQueue();

        foreach ($topics as $topicName => $partitions) {

            $consumerTopic = self::$consumerInstance->newTopic($topicName, $topicConf);

            foreach ($partitions as $partition) {
                $consumerTopic->consumeQueueStart($partition, $this->consumeConfig->consumeOffset, $queue);
            }
        }

        while (true) {
            $message = $queue->consume($this->consumeConfig->consumeTimeout);

            if (!$message instanceof Message) {
                break;
            }

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    if (is_callable($callback)) {
                        $callback($message, self::$consumerInstance);
                    }

                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
            }

        }

    }


    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

}