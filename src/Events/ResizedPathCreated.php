<?php

namespace Hpkns\Picturesque\Events;

use Hpkns\Picturesque\Format;

class ResizedPathCreated
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $resizedPath;

    /*
     * @var \Hpkns\Picturesque\Format
     */
    public $format;

    /**
     * Initialize the instance.
     *
     * @param  string $path
     * @param  string $resized_path
     * @param  \Hpkns\Picturesque\Format $format
     */
    public function __construct($path, $resized_path, Format $format)
    {
        $this->path = $path;
        $this->resizedPath = $resized_path;
        $this->format = $format;
    }
}
