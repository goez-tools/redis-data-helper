<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\MultiDriver;
use PHPUnit\Framework\TestCase;
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
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ])->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_keys_with_wildcard_string()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = [
            'testing:abc',
            'testing:def',
            'testing:ghi',
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key('testing:*')->keys();

        sort($expected);
        sort($result);
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
    public function it_should_get_dictionary_with_wildcard_string()
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
        $result = $driver->key('testing:*')->withKey()->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_keys_count()
    {
        $keys = [
            $this->assembleKey('abc'),
            $this->assembleKey('def'),
            $this->assembleKey('ghi'),
        ];
        $expected = 3;

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_zero()
    {
        $keys = [];
        $expected = 0;

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keys)->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_one()
    {
        $key = 'example';
        $expected = 1;

        $this->testRedisClient->set($key, 1);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($key)->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_keys_count_by_scan()
    {
        $expected = 3;
        $keyPattern = $this->assembleKey('a-*');
        $keys = [
            $this->assembleKey('a-1'),
            $this->assembleKey('a-2'),
            $this->assembleKey('a-3'),
            $this->assembleKey('b-4'),
        ];
        $this->testRedisClient->set($keys[0], 1);
        $this->testRedisClient->set($keys[1], 2);
        $this->testRedisClient->set($keys[2], 3);

        $driver = new MultiDriver($this->testRedisClient);
        $result = $driver->key($keyPattern)->useScan()->count();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_get_multiple_value_with_key_by_scan()
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
        $actual = $driver->key($keyPattern)->useScan(1)->get();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual);
    }
}
