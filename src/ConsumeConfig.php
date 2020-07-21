<?php


namespace kafka;


class ConsumeConfig extends Config
{

    public $offsetStoreMethod = 'broker';

    public $autoOffsetReset = 'smallest';

    public $consumeOffset = RD_KAFKA_OFFSET_STORED;

    public $consumeTimeout = 120 * 1000;


}