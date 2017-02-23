<?php

namespace Hpkns\Picturesque;

class FormatRepository
{
    /**
     *  A list of formats known to the instance
     *
     * @var array
     */
    protected $formats;

    /**
     * An formats in bulk.
     *
     * @param  array $formats
     * @return $this
     */
    public function addFormats($formats)
    {
        foreach ($formats as $name => $format) {
            $this->formats[$name] = Format::fromArray($name, $format);
        }

        return $this;
    }

    /**
     * Return a format.
     *
     * @param  mixed $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->formats[$name];
    }
}
