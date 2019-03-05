<?php

namespace Goez\RedisDataHelper\Drivers;

/**
 * Class String
 * @package Goez\RedisDataHelper\DataDrivers
 */
class ScanDriver extends AbstractDriver
{
    /**
     * @param $cursor
     * @param $count
     * @return array
     */
    public function exec($cursor, $count)
    {
        $options = [
            'MATCH' => $this->key,
            'COUNT' => $count,
        ];

        return $this->client->scan($cursor, $options);
    }
}
