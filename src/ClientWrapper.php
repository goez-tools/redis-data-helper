<?php

namespace Goez\RedisDataHelper;

use Goez\RedisDataHelper\Drivers\MultiDriver;
use Goez\RedisDataHelper\Drivers\SetsDriver;
use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use Goez\RedisDataHelper\Drivers\StringDriver;
use Goez\RedisDataHelper\Drivers\HashDriver;
use Predis\Client;
use Predis\ClientContextInterface;

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
     * @param ClientContextInterface $client
     * @return ClientWrapper
     */
    public function createFromPipeline(ClientContextInterface $client)
    {
        $this->client = $client;
        return $this;
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
     * @param $key
     * @return HashDriver
     */
    public function hash($key)
    {
        $driver = new HashDriver($this->client);
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

    /**
     * @param \Closure $callback
     */
    public function transact(\Closure $callback)
    {
        $this->client->multi();
        $callback(new static($this->client));
        $this->client->exec();
    }

    /**
     * @param \Closure $callback
     * @throws \Exception
     */
    public function pipeline(\Closure $callback)
    {
        $origClient = $this->client;
        $pipeline = $this->client->pipeline();
        $callback($this->createFromPipeline($pipeline));
        $pipeline->execute();
        $this->client = $origClient;
    }
}
