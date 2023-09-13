<?php

namespace Ptuchik\CoreUtilities\Helpers;

use Illuminate\Support\Arr;

use function array_replace;


class DataStorage
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    public function set(string $key, $value)
    {
        Arr::set($this->data, $key, $value);
        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setMany(array $data)
    {
        $this->data = array_replace($this->data, $data);
        return $this;
    }

    /**
     * @param array $keys
     *
     * @return $this
     */
    public function unset(array $keys)
    {
        Arr::forget($this->data, $keys);
        return $this;
    }

    /**
     * @param string $key
     * @param        $default
     *
     * @return array|\ArrayAccess|mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    public function only(array $keys)
    {
        $data = [];

        foreach ($keys as $key) {
            if ($value = $this->get($key)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param $keys
     *
     * @return array
     */
    public function except($keys)
    {
        $data = $this->data;
        return Arr::except($data, $keys);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key)
    {
        return Arr::exists($this->data, $key);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }
}
