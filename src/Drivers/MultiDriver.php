<?php

namespace Goez\RedisDataHelper\Drivers;

class MultiDriver extends AbstractDriver
{
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
        $keys = !is_array($this->key) ?
            $this->client->keys((string)$this->key) :
            $this->key;

        $result = $this->client->mget($keys);
        if (is_array($result)) {
            return array_map(function ($item) use ($callback) {
                $value = json_decode($item, true);
                return is_callable($callback) ? $callback($value) : $value;
            }, $result);
        }
        return $result;
    }
}
