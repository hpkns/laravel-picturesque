<?php

namespace Hpkns\Picturesque;

class Format
{
    /**
     * @var string
     */
    public $name;
    public $width;
    public $height;
    public $crop = false;
    public $options = [];

    public function __construct($name, $width = null, $height = null, $crop = false, $options)
    {
        if (empty($width) && empty($height)) {
            throw new \LogicException('Both width and height cannot be empty');
        }

        if (empty($name)) {
            $name = $width . 'x' . ($height ?: '_') . ($crop ? '-cropped' : '');
        }

        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->crop = $crop;
        $this->options = $options;
    }

    /**
     * Create a format.
     *
     * @param mixed $details
     * @return static
     */
    public static function make($details)
    {
        if (is_string($details) && is_json($details)) {
            $details = json_decode($details);
        }

        $f = (array)$details;

        return new static($f['name'], $f['width'], $f['height'], $f['crop'], $f['options']);
    }

    /**
     * Create a format from an array.
     *
     * @param  string $namee6e6e6
     * @param  array $format
     * @return static
     */
    public static function fromArray($name = null, array $format)
    {

        switch(count($format)) {
        case 1:
            return new static($name, $format[0], null, false, []);
        case 2:
            return new static($name, $format[0], $format[1], false, []);
        case 3:
            return new static($name, $format[0], $format[1], $format[2], []);
        default:
            return new static($name, $format[0], $format[1], $format[2], $format[3]);
        }
    }

    /**
     * Convert the format to JSON.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode([
        	'name'      => $this->name,
        	'width'     => $this->width,
        	'height'    => $this->height,
        	'crop'      => $this->crop,
            'options'   => $this->options,
        ]);
    }
}
