<?php namespace Hpkns\Picturesque;

use Illuminate\Html\HtmlBuilder as Builder;

class Picture {

    /**
     * The link to the original file
     *
     * @var string
     */
    protected $path;

    /**
     * The alt attribute
     *
     * @var string
     */
    protected $alt;

    /**
     * A resizing tool
     *
     * @var \Hpkns\Picturesque\Resizer
     */
    protected $resizer;

    /**
     * An HTML builder to parse html attributes
     *
     * @var \Hpkns\Picturesque\Resizer
     */
    protected $builder;

    public function __construct($path = null, $alt = null, Resizer $resizer = null, Builder $builder = null)
    {
        $this->path = $path;
        $this->alt = $alt;
        $this->resizer = $resizer ?: new Resizer;
        $this->builder = $builder ?: new Builder;
    }

    /**
     * Return the tag for the desired format
     *
     * @param  string $format
     * @param  array  $attributes
     * @return string
     */
    public function getTag($format = 'full', $attributes = [])
    {
        if( ! isset($attributes['alt']) )
        {
            $attributes['alt'] = $this->alt;
        }

        if($path = $this->resizer->getPath($this->path, $format))
        {
            $size = getimagesize($path);

            $attributes['width'] = $size[0];
            $attributes['height'] = $size[1];

            $attributes = $this->builder->attributes($attributes);

            return "<img src='{$path}'{$attributes}>";
        }
    }

    /**
     * Create dynamic properties to return tags
     *
     * @param  string $key
     * @return string
     * @throws \Exception
     */
    public function __get($key)
    {
        if($this->resizer->formatExists($key))
        {
            return $this->getTag($key);
        }
        throw new \Exception("Format $key does not exist. Please edit your config file to add it or use another format");
    }
}
