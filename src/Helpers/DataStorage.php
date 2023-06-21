<?php

namespace Ptuchik\CoreUtilities\Helpers;

use Illuminate\Support\Arr;

class DataStorage
{
    protected $data = [];

    public function set(string $key, $value)
    {
        Arr::set($this->data, $key, $value);
        return $this;
    }

    public function get(string $key)
    {
        return Arr::get($this->data, $key);
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