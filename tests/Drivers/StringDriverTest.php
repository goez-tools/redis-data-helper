<?php

namespace Tests\Drivers;

use Carbon\Carbon;
use Goez\RedisDataHelper\Drivers\StringDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class StringDriverTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_set_a_string()
    {
        $key = $this->assembleKey('example');
        $expected = '"123456"';

        $driver = new StringDriver($this->testRedisClient);
        $driver->key($key)->set('123456');
        $actual = $this->testRedisClient->get($key);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_set_a_array()
    {
        $key = $this->assembleKey('example');
        $expected = '{"abc":"123456"}';

        $driver = new StringDriver($this->testRedisClient);
        $driver->key($key)->set(['abc' => '123456']);
        $actual = $this->testRedisClient->get($key);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_nothing()
    {
        $key = $this->assembleKey('example');

        $driver = new StringDriver($this->testRedisClient);
        $actual = $driver->key($key)->get();

        $this->assertNull($actual);
    }

    /**
     * @test
     */
    public function it_should_get_expected_value()
    {
        $key = $this->assembleKey('example');
        $expected = '123456';

        $this->testRedisClient->set($key, '"123456"');
        $driver = new StringDriver($this->testRedisClient);
        $actual = $driver->key($key)->get();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_not_exist()
    {
        $driver = (new StringDriver($this->testRedisClient))->key('testing:abc');
        $this->assertFalse($driver->exists());
    }

    /**
     * @test
     */
    public function it_should_exist()
    {
        $driver = (new StringDriver($this->testRedisClient))->key('testing:abc');
        $driver->set(123);
        $this->assertTrue($driver->exists());
    }

    /**
     * @test
     */
    public function it_should_be_deleted()
    {
        $key = $this->assembleKey('example');
        $this->testRedisClient->set($key, '"123456"');
        $driver = new StringDriver($this->testRedisClient);
        $this->assertEquals(1, $driver->key($key)->delete());
    }

    /**
     * @test
     */
    public function it_should_expire_at_given_timestamp()
    {
        $key = $this->assembleKey('example');
        $timestamp = Carbon::now()->subSecond()->timestamp;

        /** @var StringDriver $driver */
        $driver = new StringDriver($this->testRedisClient);
        $driver->key($key)->set('123');
        $driver->expireAt($timestamp);
        $this->assertFalse($driver->exists());
    }

}
