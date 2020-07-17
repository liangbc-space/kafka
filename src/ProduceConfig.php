<?php


namespace kafka;


class ProduceConfig extends Config
{

    /**
     * @var int topic消息生产模式
     *          0：这意味着生产者producer不等待来自broker同步完成的确认继续发送下一条（批）消息
     *          1：这意味着producer在leader已成功收到的数据并得到确认后发送下一条message
     *          -1：这意味着producer在follower副本确认接收到数据后才算一次发送完成
     *
     *          三种机制，性能依次递减 (producer吞吐量降低)，数据健壮性则依次递增
     */
    public $requestRequiredMethod = 1;

}