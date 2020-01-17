<?php

namespace Goez\RedisDataHelper\Drivers;

use Goez\RedisDataHelper\ClientWrapper;

class MultiDriver extends AbstractDriver
{
    /**
     * @var bool
     */
    private $withKey = false;

    /**
     * @var bool
     */
    private $useScan = false;

    /**
     * 每次 SCAN 時的 COUNT 數
     *
     * @var int
     */
    private $countPerScan;

    /**
     * @return MultiDriver
     */
    public function withKey()
    {
        $this->withKey = true;
        return $this;
    }

    /**
     * @param int|null $countPerScan
     * @return $this
     */
    public function useScan($countPerScan = null)
    {
        $this->useScan = true;
        $this->countPerScan = (!$countPerScan) ? $this->getDefaultCountPerScan() : $countPerScan;
        return $this;
    }

    /**
     * 取得預設的 countPerScan
     *
     * @return int
     */
    private function getDefaultCountPerScan()
    {
        $totalKeys = $this->client->dbsize();
        return ceil($totalKeys / 10);
    }

    /**
     *
     * @return int
     */
    public function delete()
    {
        $keys = !is_array($this->key) ?
            $this->client->keys((string)$this->key) :
            $this->key;
        if (!empty($keys)) {
            return $this->client->del($keys);
        }
        return 0;
    }

    /**
     * @return array|string
     */
    public function keys()
    {
        return $this->getKeys($this->key);
    }

    /**
     * @param callable|null $callback
     * @return array
     */
    public function get(callable $callback = null)
    {
        if (empty($this->key)) {
            return [];
        }
        $keys = $this->getKeys($this->key);
        if (empty($keys)) {
            return [];
        }
        $result = $this->client->mget($keys);
        $values = array_map(function ($item) use ($callback) {
            $value = json_decode($item, true);
            return is_callable($callback) ? $callback($value) : $value;
        }, $result);
        return $this->withKey ? array_combine($keys, $values) : $values;
    }

    /**
     * @return int
     */
    public function count()
    {
        $keys = (array)$this->getKeys($this->key);
        return count($keys);
    }

    /**
     * @param array|string $key
     * @return array|string
     */
    private function getKeys($key)
    {
        if (is_array($key)) {
            return $key;
        }

        if ($this->useScan) {
            return $this->scanAll((string)$key);
        } else {
            return $this->client->keys((string)$key);
        }
    }

    /**
     * @param string $key
     * @return array
     */
    private function scanAll($key)
    {
        $countPerScan = $this->countPerScan;
        if (empty($key)) {
            return [];
        }

        $cursor = '0';
        $bufferArray = [];
        $options = [
            'MATCH' => $key,
            'COUNT' => $countPerScan,
        ];

        do {
            [$cursor, $result] = $this->client->scan($cursor, $options);
            $bufferArray[] = $result;
        } while ($cursor !== '0');

        return $this->flattenArray($bufferArray);
    }

    /**
     * Flatten 2D array into 1D array
     * @param $array
     * @return array
     */
    private function flattenArray($array)
    {
        $result = [];
        foreach ($array as $item) {
            foreach ($item as $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * @deprecated
     * @param \Closure $callback
     */
    public function transact(\Closure $callback)
    {
        $clientWrapper = new ClientWrapper($this->client);
        $clientWrapper->transact($callback);
    }
}
