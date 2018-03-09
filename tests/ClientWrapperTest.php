<?php

namespace Tests;

use Goez\RedisDataHelper\ClientWrapper;
use Goez\RedisDataHelper\Drivers\MultiDriver;
use Goez\RedisDataHelper\Drivers\SetsDriver;
use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use Goez\RedisDataHelper\Drivers\StringDriver;
use PHPUnit_Framework_TestCase as TestCase;

class ClientWrapperTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = '';

    /**
     * @test
     */
    public function it_should_get_instance_of_driver()
    {
        $wrapper = new ClientWrapper($this->testRedisClient);
        $this->assertInstanceOf(StringDriver::class, $wrapper->string('abc'));
        $this->assertInstanceOf(SetsDriver::class, $wrapper->sets('abc'));
        $this->assertInstanceOf(SortedSetsDriver::class, $wrapper->sortedSets('abc'));
        $this->assertInstanceOf(MultiDriver::class, $wrapper->multi('abc'));
    }
}
