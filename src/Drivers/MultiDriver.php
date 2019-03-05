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
     * @return array|string
     */
    public function keys()
    {
        return $this->getKeys($this->key);
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
        $keys = $this->getKeys($this->key);
        if (empty($keys)) {
            return [];
        }
        $result = $this->client->mget($keys);
        $values = array_map(function ($item) use ($callback) {
            $value = json_decode($item, true);
            return is_callable($callback) ? $callback($value) : $value;
        }, $result);
        return $this->withKey ? array_combine($keys, $values) : $values;
    }

    /**
     * @param $cursor
     * @param $count
     * @return array
     * [
     *     '5',
     *     [
     *         'find_key_1',
     *         'find_key_2',
     *     ],
     * ]
     */
    public function scan($cursor, $count)
    {
        $options = [
            'MATCH' => $this->key,
            'COUNT' => $count,
        ];

        return $this->client->scan($cursor, $options);
    }

    /**
     * @return int
     */
    public function count()
    {
        $keys = (array)$this->getKeys($this->key);
        return count($keys);
    }

    /**
     * @param array|string $key
     * @return array|string
     */
    private function getKeys($key)
    {
        return is_array($key) ?
            $key :
            $this->client->keys((string)$key);
    }

    /**
     * @deprecated
     * @param \Closure $callback
     */
    public function transact(\Closure $callback)
    {
        $clientWrapper = new ClientWrapper($this->client);
        $clientWrapper->transact($callback);
    }
}
