<?php

namespace kafka;

interface ConsumerInterface
{

    public function consumer(array $topics, callable $callback);

}