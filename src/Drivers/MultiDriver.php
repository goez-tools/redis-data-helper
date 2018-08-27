<?php

namespace Goez\RedisDataHelper\Drivers;

use Goez\RedisDataHelper\ClientWrapper;

class MultiDriver extends AbstractDriver
{
    /**
     * @var bool
     */
    private $withKey = false;

    /**
     * @return MultiDriver
     */
    public function withKey()
    {
        $this->withKey = true;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function delete()
    {
        $keys = !is_array($this->key) ?
            $this->client->keys((string)$this->key) :
            $this->key;
        if (!empty($keys)) {
            return $this->client->del($keys);
        }
        return 0;
    }

    /**
     * @param callable|null $callback
     * @return array
     */
    public function get(callable $callback = null)
    {
        if (empty($this->key)) {
            return [];
        }
        $keys = is_array($this->key) ?
            $this->key :
            $this->client->keys((string)$this->key);
        if (empty($keys)) {
            return [];
        }
        $result = $this->client->mget($keys);
        $values = array_map(function ($item) use ($callback) {
            $value = json_decode($item, true);
            return is_callable($callback) ? $callback($value) : $value;
        }, $result);
        return $this->withKey ? array_combine($this->key, $values) : $values;
    }

    /**
     * @param \Closure $callback
     */
    public function transact(\Closure $callback)
    {
        $clientWrapper = new ClientWrapper($this->client);
        $clientWrapper->transact($callback);
    }
}
