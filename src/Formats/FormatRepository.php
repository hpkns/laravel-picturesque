<?php

namespace Hpkns\Picturesque\Formats;

use ArrayAccess;
use IteratorAggregate;

class FormatRepository implements ArrayAccess, IteratorAggregate
{
    /**
     * The formats known to the repository.
     *
     * @var array
     */
    protected $formats = [];

    /**
     * Initialize the repository.
     *
     */
    public function __construct()
    {
        $this->formats['_native'] = new Format(PHP_INT_MAX, PHP_INT_MAX);
    }

    /**
     * @param  array $formats
     * @return $this
     */
    public function addFormats($formats = [])
    {
        foreach ($formats as $name => $options)
        {
            $this->formats[$name] = new Format($name, ...$options);
        }
    }

    /**
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset);
    }

    /**
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (!array_key_exists($offset, $this->formats)) {
            throw new \LogicException("Format {$offset} does not exist");
        }

        return $this->formats[$offset];
    }

    /**
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->formats[$offset] = $value;
    }

    /**
     * @param  mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->formats[$offset]);
    }

    /**
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->formats);
    }
}
