<?php

namespace Ptuchik\CoreUtilities\Helpers;

use Illuminate\Support\Arr;

use function array_replace;

class DataStorage
{
    protected $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function set(string $key, $value)
    {
        Arr::set($this->data, $key, $value);
        return $this;
    }

    public function setMany(array $data)
    {
        $this->data = array_replace($this->data, $data);
        return $this;
    }

    public function unset(string $keys)
    {
        Arr::forget($this->data, $keys);
        return $this;
    }

    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    public function except($keys)
    {
        $data = $this->data;
        return Arr::except($data, $keys);
    }

    public function has(string $key)
    {
        return Arr::exists($this->data, $key);
    }

    public function all()
    {
        return $this->data;
    }
}
