<?php

namespace Hpkns\Picturesque;

use Illuminate\Contracts\Support\Jsonable;
use Hpkns\Picturesque\Support\Concerns\HasAttributes;

class Format
{
    use HasAttributes;

    /**
     * The name of the format.
     *
     * @var string
     */
    public $name;

    /**
     * Initialize the format.
     *
     * @param string $name
     * @param int    $width
     * @param int    $height
     * @param bool   $crop
     * @param array  $options
     */
    public function __construct($name, $attributes = [])
    {
        $this->name = $name;

        $this->fill($attributes);
    }

    /**
     * Create a format from a config.
     *
     * @param  string $name
     * @param  array  $config
     * @return static
     */
    static public function fromConfig($name, array $config)
    {
        $attributes = ['filters' => array_get($config, 'filters', [])];

        list($attributes['width'], $attributes['height']) = array_pad(array_filter($config, 'is_int'), 2, null);

        foreach (['crop', 'no_resize', 'no_cache', 'force_sync', 'force_async', 'data_url'] as $flag) {
            $attributes[$flag] = in_array($flag, $config);
        }

        return new static($name, $attributes);
    }

    /**
     *
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }
}
