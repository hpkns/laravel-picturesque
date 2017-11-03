<?php

namespace Hpkns\Picturesque;

use ArrayAccess;
use IteratorAggregate;

class FormatRepository
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
        $this->formats['native'] = Format::fromConfig('native', ['no_resize']);
    }

    /**
     * @param  array $formats
     * @return $this
     */
    public function add($name, array $format)
    {
        $this->formats[$name] = Format::fromConfig($name, $format);
    }

    /**
     * Get a format
     *
     * @param  string $name;
     * @return \Hpkns\Picturesque\Formats\Format;
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->formats)) {
            return $this->formats[$name];
        } else {
            throw new \RangeException("Format $name does not exists");
        }
    }
}
