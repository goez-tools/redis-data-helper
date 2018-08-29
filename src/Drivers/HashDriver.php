<?php

namespace Goez\RedisDataHelper\Drivers;

/**
 * Class Hash
 * @package Goez\RedisDataHelper\DataDrivers
 */
class HashDriver extends AbstractDriver
{
    /**
     * @param $field
     * @param $value
     * @return mixed
     */
    public function set($field, $value)
    {
        return $this->client->hset($this->key, $field, json_encode($value));
    }

    /**
     * @param $field
     * @return mixed
     */
    public function get($field)
    {
        return json_decode($this->client->hget($this->key, $field), true);
    }
}