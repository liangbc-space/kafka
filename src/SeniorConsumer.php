<?php

namespace kafka;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicConf;

class SeniorConsumer extends Consumer implements ConsumerInterface
{

    /** @var bool 是否自动提交偏移量 */
    public $autoCommit = false;


    public function getInstance($groupId)
    {
        $conf = new Conf();

        $conf->setRebalanceCb(function (KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    /*echo "Assign: ";
                    var_dump($partitions);*/
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    /*echo "Revoke: ";
                    var_dump($partitions);*/
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });

        $conf->set('group.id', $groupId);

        $conf->set('metadata.broker.list', $this->consumeConfig->brokers);

        $conf->set('enable.auto.commit', intval($this->autoCommit));

        if (function_exists('pcntl_sigprocmask')) {
            pcntl_sigprocmask(SIG_BLOCK, array(SIGIO));
            $conf->set('internal.termination.signal', SIGIO);
        } else {
            $conf->set('queue.buffering.max.ms', 1);
        }

        $topicConf = new TopicConf();

        $topicConf->set("auto.commit.interval.ms", $this->autoCommitIntervalMs);
        $topicConf->set("auto.offset.reset", $this->consumeConfig->autoOffsetReset);

        $conf->setDefaultTopicConf($topicConf);

        self::$consumerInstance = new KafkaConsumer($conf);

    }

    /**
     * @param array $topics ['topicName1', 'topicName2']
     * @param callable $callback
     * @throws \RdKafka\Exception
     */

    public function consumer(array $topics, callable $callback)
    {
        if (!self::$consumerInstance instanceof KafkaConsumer)
            throw new \Exception('请先创建一个消费者实例');

        self::$consumerInstance->subscribe($topics);

        while (true) {
            $message = self::$consumerInstance->consume($this->consumeConfig->consumeTimeout);

            if (!$message instanceof Message) {
                break;
            }

            $topicNames = implode(',', array_filter($topics));

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    if (is_callable($callback)) {
                        $callback($message, self::$consumerInstance);
                    }
                    //消费完成后手动提交offset
                    //self::$consumerInstance->commit($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "【{$topicNames}】 No more messages; will wait for more" . PHP_EOL;
                    $callback($message, self::$consumerInstance);
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "【{$topicNames}】 Timed out" . PHP_EOL;
                    $callback($message, self::$consumerInstance);
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