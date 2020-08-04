<?php


namespace kafka;


abstract class Config
{
    /** @var string $brokers */
    public $brokers;

    /** @var int $logLevel */
    public $logLevel = LOG_DEBUG;

    /** @var bool 输出debug调试信息 */
    public $debug = false;


    public function __construct($brokers, $logLevel = LOG_DEBUG)
    {
        $this->brokers = $brokers;
        $this->logLevel = $logLevel;
    }

}