<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\MultiDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class MultiDriverTest extends TestCase
{
    use InitTestRedisClient;

    /**
     * @var string
     */
    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_delete_nothing()
    {
        $driver = new MultiDriver($this->testRedisClient);
        $count = $driver->key('nothing')->delete();
        $this->assertEquals(0, $count);
    }

    /**
     * @test
     */
    public function it_should_delete_keys()
    {
        $keyPattern = $this->assembleKey('*');
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $result = $this->testRedisClient->mget($this->testRedisClient->keys($keyPattern));
        $this->assertCount(3, $result);

        $driver = new MultiDriver($this->testRedisClient);
        $count = $driver->key($keyPattern)->delete();
        $this->assertEquals(3, $count);
    }

    /**
     * @test
     */
    public function it_should_get_multiple_value_with_key_pattern()
    {
        $keyPattern = $this->assembleKey('*');
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expect = [1, 2, 3,];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keyPattern)->get();
        $this->assertEquals(sort($expect), sort($result));
    }

    /**
     * @test
     */
    public function it_should_get_multiple_value_with_key_array()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expect = [1, 2, 3,];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->get();
        $this->assertEquals($expect, $result);
    }
}
