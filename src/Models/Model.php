<?php

namespace Ptuchik\CoreUtilities\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Spatie\Translatable\HasTranslations;

/**
 * Class Model
 * @package Ptuchik\CoreUtilities\Models
 */
class Model extends BaseModel
{
    use HasTranslations;

    /**
     * Array of translatable attributes
     * @var array
     */
    public $translatable = [];

    /**
     * This parameter is for enabling and disabling sanitization of model attributes
     * @var bool
     */
    protected $sanitize = true;

    /**
     * If model attributes sanitization is enabled, this array will
     * hold the attributes which will be ignored
     * @var array
     */
    protected $unsanitized = [];

    /**
     * Set raw attribute
     *
     * @param      $key
     * @param null $value
     *
     * @return $this
     */
    public function setRawAttribute($key, $value = null)
    {
        $attributes = $this->attributes;
        $attributes[$key] = $value;
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get raw attribute
     *
     * @param $key
     *
     * @return null
     */
    public function getRawAttribute($key)
    {
        return !empty($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Sanitize value
     *
     * @param $value
     *
     * @return mixed
     */
    public function sanitizeValue($value)
    {
        // If sanitization is disabled for the model, return unattended value
        if (!$this->sanitize) {
            return $value;
        }

        // If provided value is string, strip the tags and return
        if (is_string($value)) {
            return strip_tags($value);

            // If provided value is array, loop through each member and recursively sanitize
        } elseif (is_array($value)) {
            $sanitizedValue = [];
            foreach ($value as $key => $oldValue) {
                $sanitizedValue[$key] = $this->sanitizeValue($oldValue);
            }

            // Return sanitized array
            return $sanitizedValue;

            // In all other cases return unattended value
        } else {
            return $value;
        }
    }

    /**
     * Get sanitized attribute
     *
     * @param $key
     *
     * @return mixed
     */
    public function getSanitizedAttribute($key)
    {
        return $this->sanitizeValue($this->getAttribute($key));
    }

    /**
     * Set sanitized attribute
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setSanitizedAttribute($key, $value)
    {
        return $this->setAttribute($key, $this->sanitizeValue($value));
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param  string $key
     *
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        $value = parent::getAttributeFromArray($key);

        return in_array($key, $this->unsanitized) ? $value : $this->sanitizeValue($value);
    }

    /**
     * Get instance attribute
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (method_exists($this, $key)) {
            return parent::getAttribute($key);
        }

        return parent::getAttribute(snake_case($key));
    }

    /**
     * Set instance attribute
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $key = snake_case($key);

        $value = in_array($key, $this->unsanitized) ? $value : $this->sanitizeValue($value);

        // Check if the attribute is translatable, set it as current locale translation
        if (in_array($key, $this->translatable)) {
            return $this->setTranslation($key, config('app.locale'), $value);
        }

        // Otherwise call the native setter
        return parent::setAttribute(snake_case($key), $value);
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        // Convert all snake cased attributes to camel case on array conversion
        $array = parent::toArray();
        $camelArray = [];
        foreach ($array as $name => $value) {
            $camelArray[camel_case($name)] = $value;
        }

        return $camelArray;
    }

    /**
     * Get translations
     *
     * @param $key
     *
     * @return array
     */
    public function getTranslations($key) : array
    {
        $this->guardAgainstUntranslatableAttribute($key);

        $value = $this->getAttributes()[$key] ?? '';

        if (($this->casts[$key] ?? '') == 'array' && !is_array($this->getTranslationValue($value))) {
            return [config('app.locale') => []];
        }

        return json_decode($value, true) ?: [];
    }

    /**
     * Get translation value
     *
     * @param $value
     *
     * @return string
     */
    public function getTranslationValue($value)
    {
        $value = json_decode($value, true);

        return $value[config('app.locale')] ?? $value[config('translatable.fallback_locale')] ?? '';
    }

    /**
     * Convert the model's attributes to an array.
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($this->translatable as $attribute) {
            $attributes[$attribute] = $this->$attribute;
        }

        return $attributes;
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        // Get parent's casted attribute
        $value = parent::castAttribute($key, $value);

        // If it is null, convert to corresponding type anyways
        if (is_null($value)) {
            switch ($this->getCastType($key)) {
                case 'int':
                case 'integer':
                    return 0;
                case 'real':
                case 'float':
                case 'double':
                    return 0.00;
                case 'string':
                    return '';
                case 'bool':
                case 'boolean':
                    return false;
                case 'object':
                    return (object) [];
                case 'array':
                case 'json':
                    return [];
                case 'collection':
                    return collect([]);
                default:
                    return $value;
            }
        }

        // Return casted value
        return $value;
    }
}