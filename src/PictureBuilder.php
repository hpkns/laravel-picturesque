<?php

namespace Hpkns\Picturesque;

use Hpkns\Picturesque\Contracts\PictureResizerContract as Resizer;

class PictureBuilder
{
    /**
     * An object that holds the information about the different formats.
     *
     *
     */
    protected $formats;

    /**
     * A resizer.
     *
     * @var Contracts\PictureResizerContract
     */
    protected $resizer;

    /**
     * The path to the cache folder from the public directory
     *
     * @var string
     */
    protected $cache;

    /**
     * Initialize the instance.
     *
     * @param \Contracts\PictureResizerContract $resizer
     * @param \Illuminate\Routing\UrlGenerator  $url
     */
    public function __construct(Resizer $resizer = null, FormatRepository $formats, $cache = null)
    {
        $this->resizer = $resizer;
        $this->formats = $formats;
        $this->cache   = $cache;
    }

    /**
     * Return a resised image.
     *
     * @param string $url
     * @param string $format
     * @param string $alt
     * @param array  $attributes
     * @param bool   $secure
     *
     * @return string
     */
    public function make($url, $format = '', $alt = null, $attributes = [], $secure = false)
    {
        $format = $this->formats->get($format);

        $resized = $this->getResized($url, $format);

        $attributes = attributes(array_merge([
            'alt' => $alt,
            'src' => asset(str_replace(public_path(), '', $resized), $secure)
        ], $this->getSize($format, $resized), $attributes));

        return "<img{$attributes}>";
    }

    public function getSize($format, $image)
    {
        if ($format['crop']) {
            return array_intersect_key($format, array_flip(['width', 'height']));
        } else {
            return array_combine(['width', 'height'], array_slice(getimagesize($image), 0, 2));
        }
    }

    /**
     * Return the URL of the picture at a given size.
     *
     * @param string $format
     * @param bool   $secure
     *
     * @return string
     */
    public function makeUrl($url, $format, $secure)
    {
        $format = $this->formats->get($format);

        $url = $this->getResized($url, $format);

        return asset(str_replace(public_path(), '', $url), $secure);
    }

    /**
     * Users the resizer to resize the image.
     *
     * @param string $url
     * @param string $format
     *
     * @return string
     */
    protected function getResized($url, $format)
    {
        $input = $this->realPath($url);
        $output = $this->getOutputPath($input, $format['postfix']);

        return $this->resizer->resize($input, $output, $format);
    }

    /**
     * Return the path for a file relative to Laravel's public dir.
     *
     * @param string $path
     *
     * @return string
     */
    protected function publicPath($path)
    {
        return str_replace(public_path(), '', $path);
    }

    /**
     * Return the path from the system root for a file.
     *
     * @param string $path
     *
     * @return string
     */
    protected function realPath($url)
    {
        $path = realpath(public_path().'/'.ltrim($url, '/'));
        if (!$path) {
            throw new Exceptions\WrongPathException("File {$url} not found. The path url provided must be relative to Laravel's public path.");
        }

        return $path;
    }

    /**
     * Return the output path
     *
     * @param  string $file
     * @param  string  $format_name
     * @return string
     */
    public function getOutputPath($file, $format_name)
    {
        $infos = pathinfo($file);
        $prefix = '';

        if( ! empty($this->cache)) {
            $folder = $this->cache;
            $prefix = md5($file) . '-';
            if(strpos($folder, public_path()) !== 0)
            {
                $folder = public_path() . '/' . ltrim($folder, '/');
            }
        } else {
            $folder = $infos['dirname'];
        }

        return "{$folder}/{$prefix}{$infos['filename']}-{$format_name}.{$infos['extension']}";
    }


}
