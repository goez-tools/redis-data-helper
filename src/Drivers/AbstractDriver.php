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
     * @var string|array
     */
    protected $key;

    /**
     * AbstractDriver constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
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
}
