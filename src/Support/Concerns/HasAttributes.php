<?php

namespace Hpkns\Picturesque\Support\Concerns;

use ArrayIterator;
use Carbon\Carbon;

trait HasAttributes
{
    /**
     * The attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The attributes that must be casted as dates.
     *
     * @var array
     */
    protected $dates = [
        //
    ];

    /**
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Return a jsonable version of the model.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Return an iterator to scan attributes.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
         return new ArrayIterator($this->toArray());
    }

    /**
     * Return a JSON version of the content.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Could not encode the model');
        }

        return $json;
    }

    /**
     * Fill with a set of attributes.
     *
     * @param  array $attributes
     * @return void
     */
    public function fill($attributes = [])
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Return the value of an attribute.
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $method = camel_case("get_{$key}_attribute");

        if (method_exists($this, $method)) {
            return $this->{$method}();
        } elseif (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    /**
     * Set an attribute.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $method = camel_case("set_{$key}_attribute");

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            if (in_array($key, $this->dates)) {
                $this->attributes[$key] = $this->castDate($value);
            } else {
                $this->attributes[$key] = $value;
            }
        }
    }

    /**
     * Cast a string as date.
     *
     * @return string $date
     * @return \Carbon\Carbon
     */
    public function castDate($date)
    {
        return Carbon::createFromFormat('YmdHis.\0\Z', $date, 'europe/brussels');
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ! is_null($this->getAttribute($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Get an attribute.
     *
     * @param  string $offset
     * @return mixed
     */
    public function __get($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * Set an attribute.
     *
     * @param  string $offset
     * @param  mixed  $value
     * @return void
     */
    public function __set($offset, $value)
    {
        return $this->setAttribute($offset, $value);
    }
}
