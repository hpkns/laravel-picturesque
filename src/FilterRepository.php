<?php

namespace Hpkns\Picturesque;

class FilterRepository
{
    /**
     * A list of all the filters.
     *
     * Each time a filter is requested, a model is created and replaces the
     * class string in this array.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Register a new filter.
     *
     * @param  string $name
     * @param  string $class
     * @return void
     */
    public function registerFilter($name, $class)
    {
        $this->filters[$name] = $class;
    }

    /**
     * Return a filter
     *
     * @param  string $name
     * @return \Hpkns\Picturesque\Support\Contracts\Filter
     */
    public function get($name)
    {
        //if (is_string($this->filters[$name])) {
        //    $this->filters[$name] = app($this->filters[$name]);
        //}

        return $this->filters[$name];
    }
}
