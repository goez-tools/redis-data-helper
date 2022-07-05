<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\SetsDriver;
use PHPUnit\Framework\TestCase;
use Tests\InitTestRedisClient;

class SetsDriverTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_add_list()
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

        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_a_list()
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

        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_partial_list()
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

    /**
     * @test
     */
    public function it_should_get_zero_count()
    {
        $driver = new SetsDriver($this->testRedisClient);
        $actual = $driver->key('nothing')->count();
        $this->assertEquals(0, $actual);
    }

    /**
     * @test
     */
    public function it_should_pop_a_member()
    {
        $key = $this->assembleKey('example');
        $list = [
            '"111111"',
            '"222222"',
            '"333333"',
        ];
        $this->testRedisClient->sadd($key, $list);

        $driver = (new SetsDriver($this->testRedisClient))->key($key);
        $this->assertEquals(3, $driver->count());
        $driver->pop();
        $this->assertEquals(2, $driver->count());
    }

    /**
     * @test
     */
    public function it_should_pop_multiple_members()
    {
        $key = $this->assembleKey('example');
        $list = [
            '"111111"',
            '"222222"',
            '"333333"',
            '"444444"',
            '"555555"',
        ];
        $this->testRedisClient->sadd($key, $list);

        $driver = (new SetsDriver($this->testRedisClient))->key($key);
        $this->assertEquals(5, $driver->count());
        $driver->pop(3);
        $this->assertEquals(2, $driver->count());
    }

    /**
     * @test
     */
    public function it_should_check_member_in_sets()
    {
        $key = $this->assembleKey('example');
        $list = [
            '"111111"',
            '"222222"',
        ];
        $this->testRedisClient->sadd($key, $list);

        $driver = (new SetsDriver($this->testRedisClient))->key($key);
        $this->assertTrue($driver->has('111111'));
        $this->assertFalse($driver->has('333333'));
    }

    /**
     * @test
     */
    public function it_should_remove_member_in_sets()
    {
        $key = $this->assembleKey('example');
        $list = [
            '"111111"',
            '"222222"',
        ];
        $this->testRedisClient->sadd($key, $list);

        $driver = (new SetsDriver($this->testRedisClient))->key($key);
        $this->assertEquals(1, $driver->remove('111111'));
    }

}
