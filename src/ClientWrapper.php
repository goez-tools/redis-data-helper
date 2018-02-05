<?php

namespace Goez\RedisDataHelper;

use Goez\RedisDataHelper\Drivers\MultiDriver;
use Goez\RedisDataHelper\Drivers\SetsDriver;
use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use Goez\RedisDataHelper\Drivers\StringDriver;
use Predis\Client;

/**
 * Class ClientWrapper
 * @package Goez\RedisDataHelper
 */
class ClientWrapper
{
    /**
     * @var Client
     */
    private $client;

    /**
     * ClientWrapper constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $key
     * @return StringDriver
     */
    public function string($key)
    {
        $driver = new StringDriver($this->client);
        return $driver->key($key);
    }

    /**
     * @param $key
     * @return SetsDriver
     */
    public function sets($key)
    {
        $driver = new SetsDriver($this->client);
        return $driver->key($key);
    }

    /**
     * @param $key
     * @return SortedSetsDriver
     */
    public function sortedSets($key)
    {
        $driver = new SortedSetsDriver($this->client);
        return $driver->key($key);
    }

    /**
     * @param $keys
     * @return MultiDriver
     */
    public function multi($keys)
    {
        $driver = new MultiDriver($this->client);
        return $driver->key($keys);
    }
}
