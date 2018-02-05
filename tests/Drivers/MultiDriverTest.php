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

}
