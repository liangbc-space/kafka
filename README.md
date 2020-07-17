# kafka
Based on rdkafka, kafka advanced consumers, low-level consumers and producers


# installation
```
composer require liangbincheng\kafka
```


# example

* 消费者
```
//  Initialize kafkaConsume configuration
$consumerConfig = new \kafka\ConsumeConfig('127.0.0.1:9902,127.0.0.1:9903');

//  Create low-level consumer instance
//$consumer = new \kafka\LowConsumer($kafkaConfig);

//  Create high-level consumer instance
$consumer = new \kafka\SeniorConsumer($consumerConfig);

//  Manually submit the offset
//$consumer->autoCommit = true;

//  自动提交超时自动提交时间，毫秒
//$consumer->autoCommitIntervalMs = 5 * 1000;

//  Consumer subscription message
$consumer->consumer(['topic1', 'topic2'], function (\RdKafka\Message $message, \RdKafka\KafkaConsumer $consumer) {
    var_dump($message->payload);

    //  Manually submit the offset
    $consumer->commit();
});
```




* 生产者
```
//  Initialize kafkaProduce configuration
$produceConfig = new \kafka\ProduceConfig('127.0.0.1:9902,127.0.0.1:9903');

//  Create producer instance
$producer = new \kafka\Producer($produceConfig);

//  Production news
$producer->producer('topic', 'message', 1);
```
