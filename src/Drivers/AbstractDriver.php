<?php

namespace Goez\RedisDataHelper\Drivers;

use Predis\Client;
use Predis\Transaction\MultiExec;

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
     * @var string|array
     */
    protected $key;

    /**
     * AbstractDriver constructor.
     * @param MultiExec|Client $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param string|array $key
     * @return static
     */
    public function key($key)
    {
        $this->key = is_array($key) ? $key : (string)$key;
        return $this;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return (bool)$this->client->exists($this->key);
    }

    /**
     * @return int
     */
    public function delete()
    {
        return $this->client->del([$this->key]);
    }

    /**
     * @param $timestamp
     * @return AbstractDriver|StringDriver|SetsDriver|SortedSetsDriver|MultiDriver
     */
    public function expireAt($timestamp)
    {
        $this->client->expireat($this->key, $timestamp);
        return $this;
    }
}
