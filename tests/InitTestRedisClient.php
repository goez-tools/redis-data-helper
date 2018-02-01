<?php

namespace Tests;

use Goez\RedisDataHelper\Drivers\AbstractDriver;
use Predis\Client;

/**
 * Trait InitTestRedisClient
 * @package Tests\DataDrivers
 * @property-read $keyPrefix
 */
trait InitTestRedisClient
{
    /**
     * @var Client
     */
    private $testRedisClient;

    /**
     * @before
     */
    protected function init()
    {
        $this->testRedisClient = new Client([
            'host' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
        ]);
        $this->deleteKeys();
    }

    /**
     * @afterClass
     */
    protected function destroy()
    {
        $this->deleteKeys();
    }

    /**
     * @param $key
     * @return string
     */
    private function assembleKey($key)
    {
        return $this->keyPrefix . $key;
    }

    /**
     * @return void
     */
    private function deleteKeys()
    {
        $keys = (array)$this->testRedisClient->keys($this->assembleKey('*'));
        if (!empty($keys)) {
            $this->testRedisClient->del($keys);
        }
    }
}
