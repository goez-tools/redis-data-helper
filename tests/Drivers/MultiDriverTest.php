<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\ClientWrapper;
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
        $expected = [1, 2, 3,];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $actual = $driver->key($keyPattern)->get();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
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
        $expected = [1, 2, 3,];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_an_empty_array()
    {
        $expected = [];
        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_an_empty_array_with_a_not_existing_key()
    {
        $expected = [];
        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key('example:*')->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_null_array()
    {
        $expected = [null, null, null,];
        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key([
            'abc',
            'def',
            'ghi',
        ])->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_dictionary_with_key_array()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [
            'testing:abc' => 1,
            'testing:def' => 2,
            'testing:ghi' => 3,
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->withKey()->get();
        $this->assertEquals($expected, $result);
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

        $driver = new MultiDriver($this->testRedisClient);
        $expected = [1, 2, 3,];
        $driver->transact(function (ClientWrapper $clientWrapper) use ($keys) {
            $clientWrapper->string($keys[0])->set(1);
            $clientWrapper->string($keys[1])->set(2);
            $clientWrapper->string($keys[2])->set(3);
        });
        $actual = $this->testRedisClient->mget($keys);
        $this->assertEquals($expected, $actual);
    }
}
