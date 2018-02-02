<?php

namespace Tests;

use Goez\RedisDataHelper\ClientWrapper;
use Goez\RedisDataHelper\Drivers\SetsDriver;
use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use Goez\RedisDataHelper\Drivers\StringDriver;
use \PHPUnit_Framework_TestCase as TestCase;

class ClientWrapperTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_delete_nothing()
    {
        $wrapper = new ClientWrapper($this->testRedisClient);
        $count = $wrapper->delete('nothing');
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

        $wrapper = new ClientWrapper($this->testRedisClient);
        $count = $wrapper->delete($keyPattern);
        $this->assertEquals(3, $count);
    }

    /**
     * @test
     */
    public function it_should_get_instance_of_driver()
    {
        $wrapper = new ClientWrapper($this->testRedisClient);
        $this->assertInstanceOf(StringDriver::class, $wrapper->string('abc'));
        $this->assertInstanceOf(SetsDriver::class, $wrapper->sets('abc'));
        $this->assertInstanceOf(SortedSetsDriver::class, $wrapper->sortedSets('abc'));
    }

}
