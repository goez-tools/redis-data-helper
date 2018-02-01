<?php

namespace Goez\RedisDataHelper\Drivers;

use Predis\Client;

/**
 * Class BaseDriver
 * @package Goez\RedisDataHelper\DataDrivers
 */
abstract class AbstractDriver
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $key = '';

    /**
     * AbstractDriver constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $key
     * @return static
     */
    public function key($key)
    {
        $this->key = (string)$key;
        return $this;
    }
}
