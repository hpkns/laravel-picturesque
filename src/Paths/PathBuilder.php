<?php

namespace Hpkns\Picturesque\Paths;

use Hpkns\Picturesque\Image\ImageResizer;
use Hpkns\Picturesque\Formats\Format;

class PathBuilder
{
    /**
     * The path to the cache.
     *
     * @var string
     */
    protected $cachePath;

    /**
     * The image resizer.
     *
     * @var Hpkns\Picturesque\Image\ImageResizer
     */
    protected $resizer;

    /**
     * Initialize the path builder
     *
     * @param string $cache_path
     */
    public function __construct($cache_path = null, ImageResizer $resizer)
    {
        $this->cachePath = $cache_path;
        $this->resizer = $resizer;
    }

    /**
     * Return the url for a resized picture.
     *
     * @param  string                            $path
     * @param  \Hpkns\Picturesque\Formats\Format $format
     * @param  bool                              $secure
     * @return string
     */
    public function getResizedUrl($path, Format $format, $secure = false)
    {
        if (!file_exists($path) || !is_file($path)) {
            return;
        }

        $path = $this->getResizedPath($path, $format);

        if ($path) {
            $path = str_replace(public_path(), '', $path);
            return asset($path, $secure);
        }
    }

    /**
     * Return the path for a resize picture.
     *
     * @param  string                            $path
     * @param  \Hpkns\Picturesque\Formats\Format $format
     * @return string
     */
    public function getResizedPath($path, Format $format)
    {
        if (!file_exists($path) || !is_file($path)) {
            return;
        }
        if (! $this->pictureNeedsResizing($path, $format)) {
            return $path;
        }

        $resized_path = $this->getOutputPath($path, $format);

        if (
            !array_get($format->options, 'cache', true)
            || !file_exists($resized_path)
            || filemtime($resized_path) < filemtime($path))
        {
            $resized_path = $this->resizer->handle($path, $resized_path, $format);
        } else {
            $resized_path .= '?t=' . base_convert(md5(filemtime($resized_path)), 10, 32);
        }

        return $resized_path;
    }

    /**
     *
     */
    public function pictureNeedsResizing($path, Format $format)
    {
        $size = getimagesize($path);

        return ($format->crop) || ($format->width < $size[0]) || ($format->height < $size[1]);
    }

    /**
     * Format the output path according to Picturesque's settings and the format.
     *
     * @param  string                            $path
     * @param  \Hpkns\Picturesque\Formats\Format $format
     * @return string
     */
    public function getOutputPath($path, $format)
    {
        $info = pathinfo($path);
        if ($this->cachePath) {
            $output_base = $this->cachePath . '/' . md5($path);
        } else {
            $output_base = dirname($path) . '/';
        }

        $extension = $format->option('format') ?: $info['extension'];
        return $output_base . str_slug($info['filename']) . '-' . $format->name . '.' . $extension;
    }
}
