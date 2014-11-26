<?php namespace Hpkns\Picturesque;

use Illuminate\Html\HtmlBuilder as Builder;

class Picture {

    /**
     * The link to the original file
     *
     * @var string
     */
    protected $url;

    /**
     * The alt attribute
     *
     * @var string
     */
    protected $alt;

    /**
     * An HTML builder to parse html attributes
     *
     * @param  string                     $url
     * @param  string                     $alt
     * @param  \Hpkns\Picturesque\Resizer $builder
     * @return void
     */
    protected $builder;

    public function __construct($url, $alt = null, PictureBuilder $builder = null)
    {
        $this->url = $url;
        $this->alt = $alt;
        $this->builder = $builder ?: \App::make('Hpkns\Picturesque\PictureBuilder');
    }

    /**
     * Return the tag for the desired format
     *
     * @param  string $format
     * @param  array  $attributes
     * @return string
     */
    public function getTag($format, $attributes = [], $secure = false)
    {
        return $this->builder->make($this->url, $format, $this->alt, $attributes, $secure);
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
        return $this->getTag($key);
    }

    /**
     * Return different invocations of getTag using the name of the dynamic property as size
     *
     * @param  string $key
     * @param  array  $args
     * @return string
     */
    public function __call($key, $args)
    {
        if(count($args) == 0)
        {
            return $this->getTag($key);
        }
        elseif(count($args) == 1)
        {
            return $this->getTag($key, $args[0]);
        }
        else // count >= 2
        {
            return $this->getTag($key, $args[0], $args[1]);
        }
    }

    /**
     * Convert the picture to a string
     *
     * @return string
     */
    public function __toString()
    {
        try
        {
            return $this->getTag('default');
        }
        catch(\Exception $e)
        {
            return '';
        };

    }
}
