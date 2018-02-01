<?php

namespace Goez\RedisDataHelper\Drivers;

/**
 * Class SortedSets
 * @package Goez\RedisDataHelper\DataDrivers
 */
class SortedSetsDriver extends AbstractDriver
{
    /**
     * @param array $list
     */
    public function addList(array $list)
    {
        $result = [];
        foreach ($list as $value => $score) {
            $result[json_encode((string) $value)] = $score;
        }
        $this->client->zadd($this->key, $result);
    }

    /**
     * @param int $count
     * @return array
     */
    public function getList($count = -1)
    {
        $start = ($count > 0) ? -$count : 0;
        $list = $this->client->zrange($this->key, $start, -1, 'withscores');
        $result = [];
        foreach ($list as $value => $score) {
            $result[json_decode($value, true)] = (int) $score;
        }
        return $result;
    }
}
