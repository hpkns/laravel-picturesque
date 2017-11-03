<?php

namespace Hpkns\Picturesque;

use Illuminate\Support\HtmlString;
use Hpkns\Picturesque\Image;
use Hpkns\Picturesque\Support\Contracts\ResizePromise;

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
     * @var \Hpkns\Picturesque\FormatRepository
     */
    protected $formats;

    /**
     * @var \Hpkns\Picturesque\Cache
     */
    protected $cache;

    /**
     * @var \Hpkns\Picturesque\Resizer
     */
    protected $resizer;

    /**
     * Initialize the image.
     *
     * @param string $path
     */
    public function __construct($path, array $attributes = [], FormatRepository $formats = null, Cache $cache = null, Resizer $resizer = null)
    {
        $this->path = $path;
        $this->attributes = $attributes;

        $this->formats = $formats ?: app('picturesque.formats');
        $this->cache = $cache ?: app('picturesque.cache');
        $this->resizer = $resizer ?: app('picturesque.resizer');
    }

    /**
     * Get the tag to the resized picture.
     *
     * @param  mixed $format
     * @param  array $attributes
     * @param  bool  $secure
     * @return \Illuminate\Support\HtmlString
     */
    public function getTag($format = null, $attributes = [], $secure = false)
    {
        $format = $this->getFormat($format);

        $attributes = attributes(array_merge([
            'src'    => $this->getUrl($format, $secure),
            'width'  => $format->width != PHP_INT_MAX ? $format->width : null,
            'height' => $format->height,
        ], $attributes));

        return new HtmlString("<img{$attributes}>");
    }

    /**
     * Return the URL to the resized version of the picture.
     *
     * @param  Hpkns\Picturesque\Formats\Format $format
     * @return string
     */
    public function getUrl($format = null, $secure = false)
    {
        $format = $this->getFormat($format);
        $cached_name = $this->cache->getName($this->path, $format);

        if (! $format->no_cache
            && $this->cache->has($cached_name)
            && $this->cache->newerThan($cached_name, filemtime($this->path))
        ) {
            return $this->getCachedUrl($cached_name, $format, $secure);
        } else {
            return $this->getResizedUrl($cached_name, $format, $secure);
        }
    }

    /**
     *
     *
     */
    public function getCachedUrl($cached_name, $format, $secure)
    {
        if ($format->data_url) {
            return app('picturesque')
                ->image($this->cache->get($cached_name))
                ->encode('data-url');
        } else {
            return asset($this->cache->getUrl($cached_name), $secure);
        }
    }

    /**
     *
     *
     */
    public function getResizedUrl($cached_name, $format, $secure)
    {
        $image = $this->resizer->resizeOrCreatePromise($this->path, $format);

        if ($image instanceof ResizePromise) {
            return $image->getRoute();
        } else {
            $url = $this->cache->save($image, $cached_name);

            if ($format->data_url) {
                return $image->encode('data-url');
            } else {
                return asset($url, $secure);
            }
        }
    }

    /**
     * Get the format from its string representation.
     *
     * @var mixed
     * @return \Hpkns\Picturesque\Formats\Format
     */
    public function getFormat($format)
    {
        return $format instanceof Format
            ? $format
            : $this->formats->get($format);
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
        return $this->getTag($key, ...$args);
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
