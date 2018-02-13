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
}