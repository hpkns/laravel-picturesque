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
     * The alt attribute.
     *
     * @var string
     */
    protected $alt;

    public function __construct($path, $alt = null, PathBuilder $builder = null, FormatRepository $formats = null)
    {
        $this->path = $path;
        $this->alt = $alt;
        $this->builder = $builder ?: app('picturesque.paths');
        $this->formats = $formats ?: app('picturesque.formats');
    }

    /**
     * Get the tag to the resized picture.
     *
     * @param  string|\Hpkns\Picturesque\Format $format
     * @param  array                            $attributes
     * @param  string                           $alt
     * @param  bool                             $secure
     * @return \Illuminate\Support\HtmlString
     */
    public function getTag($format_name, $attributes = [], $alt = null, $secure = false)
    {
        $format = $this->getFormat($format_name);

        $attributes = attributes([
            'alt' => $alt,
            'src' => $this->builder->getResizedUrl($this->path, $format),
            'width' => $format->width,
            'height' => $format->height,
        ]);

        return new HtmlString("<img{$attributes}>");
    }


    /**
     * Return a picture tag.
     *
     * @param  string|\Hpkns\Picturesque\Format $format
     * @param  string                           $alt
     * @param  bool                             $secure
     * @return \Illuminate\Support\HtmlString
     */
    public function getPictureTag($format, $alt = null, $secure = false) {
        if ($format = config("picturesque.picture_formats.{$format}")) {
            $html = '';

            foreach (array_get($format, 'sources', []) as $source) {
                $source['srcset'] = preg_replace_callback('/:\w+/', \Closure::bind(function($format_name) {
                    $format = $this->getFormat(ltrim($format_name[0], ':'));
                    return $this->builder->getResizedUrl($this->path, $format);
                }, $this), $source['srcset']);

                $attributes = attributes($source);
                $html .= "<source{$attributes}>";
            }

            if ($default = array_get($format, 'default')) {
                $html .= $this->getTag($default, [], $alt, $secure);
            }

            return new HtmlString("<picture>{$html}</picture>");
        }
    }

    /**
     * Get a resized picture URL.
     *
     * @param  string $format_name
     * @param  bool   $secure
     * @return string
     */
    public function getUrl($format_name, $secure = false)
    {
        $format = $this->getFormat($format_name);

        return $this->builder->getResizedUrl($this->path, $format, $secure);
    }

    /**
     * Return a raw path to a resized picture.
     *
     * @param  string $format_name
     * @return string
     */
    public function getPath($format_name)
    {
        $format = $this->getFormat($format_name);

        return $this->builder->getResizedPath($this->path, $format);
    }

    /**
     * Return a format from its name.
     *
     * @param  mixed $format_name
     * @return \Hpkns\Picturesque\Format
     */
    public function getFormat($format_name)
    {
        if ($format_name instanceof Format) {
            return $format_name;
        } else {
            return $this->formats->get($format_name);
        }
    }

    /**
     * Create dynamic properties to return tags.
     *
     * @param string $key
     *
     * @return string
     *
     * @throws \Exception
     */
    public function __get($key)
    {
        if (ends_with($key, '_picture')) {
            return $this->getPictureTag(str_replace('_picture', '', $key));
        } else {
            return $this->getTag($key);
        }
    }

    /**
     * Return different invocations of getTag using the name of the dynamic property as size.
     *
     * @param string $key
     * @param array  $args
     *
     * @return string
     */
    public function __call($key, $args)
    {
        if (count($args) == 0) {
            return $this->getTag($key);
        } elseif (count($args) == 1) {
            return $this->getTag($key, $args[0]);
        } else {
            return $this->getTag($key, $args[0], $args[1]);
        }
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
        } catch (\Exception $e) {
            return '';
        };
    }
}
