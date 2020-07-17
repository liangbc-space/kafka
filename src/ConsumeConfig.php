<?php


namespace kafka;


class ConsumeConfig extends Config
{

    public $offsetStoreMethod = 'broker';

    public $autoOffsetReset = 'smallest';

    public $consumeOffset = RD_KAFKA_OFFSET_STORED;

    public $consumeTimeout = 120 * 1000;

    public function setOffsetStoreMethod($method)
    {
        $this->offsetStoreMethod = $method;
    }

    public function setAutoOffsetReset($autoOffsetReset)
    {
        $this->autoOffsetReset = $autoOffsetReset;
    }

    public function setConsumeOffset($consumeOffset)
    {
        $this->consumeOffset = $consumeOffset;
    }

    public function setConsumeTimeout($timeout)
    {
        $this->consumeTimeout = $timeout;
    }

}