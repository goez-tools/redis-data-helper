<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\HashDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class HashDriverTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_set_a_string_in_field()
    {
        $key = $this->assembleKey('example');
        $field = 'example';
        $expected = '"123456"';
        $value = '123456';

        $driver = new HashDriver($this->testRedisClient);
        $driver->key($key)->set($field, $value);
        $actual = $this->testRedisClient->hget($key, $field);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_expected_value()
    {
        $key = $this->assembleKey('example');
        $field = 'example';
        $expected = '123456';

        $this->testRedisClient->hset($key, $field, '"123456"');
        $driver = new HashDriver($this->testRedisClient);
        $actual = $driver->key($key)->get($field);

        $this->assertEquals($expected, $actual);
    }
}