<?php

namespace Tests;

use Goez\RedisDataHelper\ClientWrapper;
use Goez\RedisDataHelper\Drivers\MultiDriver;
use Goez\RedisDataHelper\Drivers\SetsDriver;
use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use Goez\RedisDataHelper\Drivers\StringDriver;
use PHPUnit\Framework\TestCase;

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

    /**
     * @test
     */
    public function it_should_run_multiple_command()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];

        $wrapper = new ClientWrapper($this->testRedisClient);
        $expected = [1, 2, 3,];
        $wrapper->transact(function (ClientWrapper $clientWrapper) use ($keys) {
            $clientWrapper->string($keys[0])->set(1);
            $clientWrapper->string($keys[1])->set(2);
            $clientWrapper->string($keys[2])->set(3);
        });
        $actual = $this->testRedisClient->mget($keys);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_run_pipeline_command()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];

        $expected = [1, 2, 3,];
        $wrapper = new ClientWrapper($this->testRedisClient);
        $wrapper->pipeline(function (ClientWrapper $clientWrapper) use ($keys) {
            $clientWrapper->string($keys[0])->set(1);
            $clientWrapper->string($keys[1])->set(2);
        });
        $wrapper->string($keys[2])->set(3);

        $actual = $this->testRedisClient->mget($keys);
        $this->assertEquals($expected, $actual);
    }
}
