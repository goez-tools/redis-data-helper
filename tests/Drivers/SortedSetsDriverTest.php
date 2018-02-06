<?php

namespace Tests\Drivers;

use Goez\RedisDataHelper\Drivers\SortedSetsDriver;
use PHPUnit_Framework_TestCase as TestCase;
use Tests\InitTestRedisClient;

class SortedSetsDriverTest extends TestCase
{
    use InitTestRedisClient;

    protected $keyPrefix = 'testing:';

    /**
     * @test
     */
    public function it_should_add_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '111111' => 4,
            '222222' => 2,
            '333333' => 6,
        ];
        $expected = [
            '"222222"',
            '"111111"',
            '"333333"',
        ];

        $driver = new SortedSetsDriver($this->testRedisClient);
        $driver->key($key)->addList($list);

        $actual = $this->testRedisClient->zcard($key);
        $this->assertEquals(3, $actual);

        $actual = $this->testRedisClient->zrange($key, 0, -1);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_scored_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '222222' => 2,
            '111111' => 4,
            '333333' => 6,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getList();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_reversed_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '333333' => 6,
            '111111' => 4,
            '222222' => 2,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getReversedList();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_special_list_with_middleware()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '222222' => 4,
            '111111' => 16,
            '333333' => 36,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver
            ->key($key)
            ->middleware(function (array $list) {
                $result = [];
                foreach ($list as $value => $score) {
                    $result[json_decode($value, true)] = (int)$score * (int)$score;
                }
                return $result;
            })
            ->getList();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_special_reversed_list_with_middleware()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '333333' => 36,
            '111111' => 16,
            '222222' => 4,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver
            ->key($key)
            ->middleware(function (array $list) {
                $result = [];
                foreach ($list as $value => $score) {
                    $result[json_decode($value, true)] = (int)$score * (int)$score;
                }
                return $result;
            })
            ->getReversedList();

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_get_partial_list_with_given_key()
    {
        $key = $this->assembleKey('example');

        $list = [
            '"111111"' => 4,
            '"222222"' => 2,
            '"333333"' => 6,
        ];
        $expected = [
            '111111' => 4,
            '333333' => 6,
        ];

        $this->testRedisClient->zadd($key, $list);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $actual = $driver->key($key)->getList(2);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function it_could_add_array_as_item()
    {
        $key = $this->assembleKey('example');
        $expected = [
            '{"abc":1}' => 123,
        ];

        $driver = new SortedSetsDriver($this->testRedisClient);
        $driver->key($key)->add([
            'abc' => 1,
        ], 123);

        $actual = $this->testRedisClient->zrange($key, 0, -1, 'withscores');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_should_remove_item()
    {
        $key = $this->assembleKey('example');
        $expected = [
            '"def"' => 456,
        ];

        $this->testRedisClient->zadd($key, [
            '"abc"' => 123,
            '"def"' => 456,
        ]);

        $driver = new SortedSetsDriver($this->testRedisClient);
        $driver->key($key)->remove('abc');

        $actual = $this->testRedisClient->zrange($key, 0, -1, 'withscores');
        $this->assertEquals($expected, $actual);
    }
}
