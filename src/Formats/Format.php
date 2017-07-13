<?php

namespace Hpkns\Picturesque\Formats;

use Illuminate\Contracts\Support\Jsonable;

class Format
{
    /**
     * Initialize the format.
     *
     * @param string $name
     * @param int    $width
     * @param int    $height
     * @param bool   $crop
     * @param array  $options
     */
    public function __construct($name, $width = null, $height = null, $crop = false, $options = [])
    {
        if (empty($width) && empty($height)) {
            throw new \LogicException('Both width and height cannot be empty');
        }
        if (empty($name)) {
            $name = $width . 'x' . ($height ?: '_') . ($crop ? '-cropped' : '');
        }

        $this->attributes = compact('name', 'width', 'height', 'crop', 'options');
    }

    /**
     * Return an attribute.
     *
     * @param  string $key;
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Convert the format to JSON.
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->attributes, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw \ErrorException('Could not convert to JSON');
        }

        return $json;
    }

    /**
     * Get an option.
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function option($key, $default = null)
    {
        return array_get((array)$this->attributes['options'], $key, $default);
    }

    /**
     * Create a format from a JSON represenation.
     *
     * @param  string $format
     * @return static
     */
    public static function fromJson($format)
    {
        $attributes = (array)json_decode($format, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw \ErrorException('Could not extract format from JSON');
        }

        return new static(...(array_values($attributes)));
    }

    /**
     * Convert the format to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
