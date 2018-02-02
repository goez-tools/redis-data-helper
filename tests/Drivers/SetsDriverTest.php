<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\SetsDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class SetsDriverTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_add_list_to_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '111111',
            '222222',
            '333333',
        ];
        $expected = [
            '"111111"',
            '"222222"',
            '"333333"',
        ];

        $driver = new SetsDriver($this->testRedisClient);
        $driver->key($key)->addList($list);

        $actual = $this->testRedisClient->smembers($key);

        $this->assertEquals(sort($expected), sort($actual));
    }

    /**
     * @test
     */
    public function it_should_get_a_list_with_given_key()
    {
        $key = $this->assembleKey('example');
        $list = [
            '"111111"',
            '"222222"',
            '"333333"',
        ];
        $expected = [
            '111111',
            '222222',
            '333333',
        ];
        $this->testRedisClient->sadd($key, $list);

        $driver = new SetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getList();

        $this->assertEquals(3, $driver->count());
        $this->assertEquals(sort($expected), sort($actual));
    }

    /**
     * @test
     */
    public function it_should_get_partial_list_with_given_key()
    {
        $key = $this->assembleKey('example');
        $list = [
            '"111111"',
            '"222222"',
            '"333333"',
        ];
        $this->testRedisClient->sadd($key, $list);

        $driver = new SetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getList(2);

        $this->assertCount(2, $actual);
    }
}
