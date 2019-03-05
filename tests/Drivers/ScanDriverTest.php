<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\ScanDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class ScanDriverTest extends TestCase
{
    use InitTestRedisClient;

    /**
     * @var string
     */
    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_find_keys()
    {
        $keyPattern = $this->assembleKey('*');
        $key1 = $this->assembleKey('abc');
        $key2 = $this->assembleKey('def');
        $key3 = $this->assembleKey('ghi');
        $keys = [
            $key1,
            $key2,
            $key3,
        ];
        $expected = $keys;
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new ScanDriver($this->testRedisClient);
        $cursor = '0';
        $actual = [];
        do {
            list($cursor, $results) = $driver->key($keyPattern)->exec($cursor, 1);
            foreach ($results as $key) {
                $actual[] = $key;
            }
        } while ($cursor !== '0');
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_empty_response()
    {
        $expected = [];
        $cursor = '0';
        $count = 1;
        $driver = new ScanDriver($this->testRedisClient);
        list($cursor, $actual) = $driver->exec($cursor, $count);
        $this->assertEquals($expected, $actual);
    }
}
