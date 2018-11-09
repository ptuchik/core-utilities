<?php

namespace Ptuchik\CoreUtilities\Traits;

/**
 * Trait HasParams
 * @package Ptuchik\CoreUtilities\Traits
 */
trait HasParams
{
    /**
     * Set param
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setParam($key, $value)
    {
        $params = $this->params;
        $params[$key] = $value;
        $this->params = $params;

        return $this;
    }

    /**
     * Get param
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return array_get($this->params, $key, $default) ?? $default;
    }

    /**
     * Has param
     *
     * @param      $key
     *
     * @return mixed
     */
    public function hasParam($key)
    {
        return array_has($this->params, $key);
    }
}