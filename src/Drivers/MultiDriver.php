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
}
