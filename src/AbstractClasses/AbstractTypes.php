<?php

namespace Ptuchik\CoreUtilities\AbstractClasses;

use ReflectionClass;

/**
 * Class AbstractTypes
 * @package Ptuchik\CoreUtilities\AbstractClasses
 */
class AbstractTypes
{
    /**
     * Returns type values as array json or string
     *
     * @param string $returnType
     * @param bool   $translate
     *
     * @return array|string
     */
    public static function all($returnType = 'string', $translate = true)
    {
        $reflection = new ReflectionClass(get_called_class());
        $constants = $reflection->getConstants();
        if ($returnType == 'array') {
            return $constants;
        }
        if ($returnType == 'json') {
            $flipped = array_flip($constants);
            foreach ($flipped as &$value) {
                if ($translate) {
                    $value = trans(config('ptuchik-core-utilities.translations_prefix').'.'.strtolower($value));
                }
            }

            return json_encode($flipped);
        } else {
            return implode(',', $constants);
        }
    }

    /**
     * Return the key of constant by given value
     *
     * @param $value
     *
     * @return mixed
     */
    public static function key($value)
    {
        $reflection = new ReflectionClass(get_called_class());
        $constants = $reflection->getConstants();
        $flipped = array_flip($constants);

        return $flipped[$value];
    }
}