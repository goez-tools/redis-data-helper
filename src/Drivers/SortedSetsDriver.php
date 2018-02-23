<?php

namespace Goez\RedisDataHelper\Drivers;

/**
 * Class SortedSets
 * @package Goez\RedisDataHelper\DataDrivers
 */
class SortedSetsDriver extends AbstractDriver
{
    /**
     * @var callable
     */
    private $closure;

    /**
     * @param array $list
     */
    public function addList(array $list)
    {
        $result = [];
        foreach ($list as $value => $score) {
            $result[json_encode((string)$value)] = $score;
        }
        $this->client->zadd($this->key, $result);
    }

    /**
     * @param mixed $value
     * @param int $score
     */
    public function add($value, $score)
    {
        $result = [json_encode($value) => $score];
        $this->client->zadd($this->key, $result);
    }

    /**
     * @param callable $middleware
     * @return SortedSetsDriver
     */
    public function middleware(callable $middleware)
    {
        $this->closure = $middleware;
        return $this;
    }

    /**
     * @param int $count
     * @return array
     */
    public function getList($count = -1)
    {
        $start = ($count > 0) ? -$count : 0;
        $list = $this->client->zrange($this->key, $start, -1, 'withscores');
        if (is_callable($this->closure)) {
            return call_user_func($this->closure, $list);
        }

        $result = [];
        foreach ($list as $value => $score) {
            $result[json_decode($value, true)] = (int)$score;
        }
        return $result;
    }

    /**
     * @param int $count
     * @return array
     */
    public function getReversedList($count = -1)
    {
        $stop = $count === -1 ? $count : $count - 1;
        $list = $this->client->zrevrange($this->key, 0, $stop, 'withscores');
        if (is_callable($this->closure)) {
            return call_user_func($this->closure, $list);
        }

        $result = [];
        foreach ($list as $value => $score) {
            $result[json_decode($value, true)] = (int)$score;
        }
        return $result;
    }

    /**
     * @param $member
     */
    public function remove($member)
    {
        return $this->client->zrem($this->key, json_encode($member));
    }
}
