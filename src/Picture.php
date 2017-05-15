<?php

namespace Hpkns\Picturesque;

use Illuminate\Support\HtmlString;

class Picture
{
    /**
     * The link to the original file.
     *
     * @var string
     */
    protected $path;

    /**
     * A set of default attributes.
     *
     * @var array
     */
    protected $attributes;

    /**
     * Initialize the image.
     *
     * @param string $path
     */
    public function __construct($path, array $attributes = [])
    {
        $this->path = $path;
        $this->attributes = $attributes;
    }

    /**
     * Get the tag to the resized picture.
     *
     * @param  mixed $format
     * @param  array $attributes
     * @param  bool  $secure
     * @return \Illuminate\Support\HtmlString
     */
    public function getTag($format, $attributes = [], $secure = false)
    {
        $format = $this->getFormat($format);

        $attributes = attributes(array_merge($this->attributes, $attributes, [
            'src'    => $this->getResizedUrl($format, $secure),
            'width'  => $format->width,
            'height' => $format->height,
        ]));

        return new HtmlString("<img{$attributes}>");
    }

    /**
     * Return the URL to the resized version of the picture.
     *
     * @param  Hpkns\Picturesque\Formats\Format $format
     * @return string
     */
    public function getUrl($format, $secure = false)
    {
        $format = $this->getFormat($format);

        return $this->getResizedUrl($this->path, $format, $secure);
    }

    /**
     * Return the URL to the resized version of the picture.
     *
     * @param  Hpkns\Picturesque\Formats\Format $format
     * @return string
     */
    public function getResizedUrl($format, $secure = false)
    {
        return app('picturesque.paths')->getResizedUrl($this->path, $format, $secure);
    }

    /**
     * Get the format from its string representation.
     *
     * @var mixed
     * @return \Hpkns\Picturesque\Formats\Format
     */
    public function getFormat($format)
    {
        if ($format instanceof Format) {
            return $format;
        } else {
            return app('picturesque.formats')[$format];
        }
    }

    /**
     * Create dynamic properties to return tags.
     *
     * @param string $key
     * @return string
     */
    public function __get($key)
    {
        return $this->getTag($key);
    }

    /**
     * Return different invocations of getTag using the name of the dynamic property as size.
     *
     * @param  string $key
     * @param  array  $args
     * @return string
     */
    public function __call($key, $args)
    {
        $this->getTag($key, ...$args);
    }

    /**
     * Convert the picture to a string.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getTag('default');
        } catch (\Exception $e) { // TODO be more specific, maybe!
            return '';
        };
    }
}
