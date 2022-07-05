<?php

namespace Goez\RedisDataHelper\Drivers;

/**
 * Class String
 * @package Goez\RedisDataHelper\DataDrivers
 */
class StringDriver extends AbstractDriver
{
    /**
     * @param $value
     * @return mixed
     */
    public function set($value)
    {
        return $this->client->set($this->key, json_encode($value));
    }

    /**
     * @return mixed
     */
    public function get()
    {
        $value = $this->client->get($this->key);
        return $value ? json_decode($value, true) : $value;
    }
}
