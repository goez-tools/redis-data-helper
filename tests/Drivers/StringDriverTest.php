<?php

namespace Tests\Drivers;

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
    public function it_should_set_a_string_with_given_key()
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
    public function it_should_get_expected_value_with_given_key()
    {
        $key = $this->assembleKey('example');
        $expected = '123456';

        $this->testRedisClient->set($key, '"123456"');
        $driver = new StringDriver($this->testRedisClient);
        $actual = $driver->key($key)->get();

        $this->assertEquals($expected, $actual);
    }
}
