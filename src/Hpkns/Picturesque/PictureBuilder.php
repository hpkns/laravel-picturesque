<?php namespace Hpkns\Picturesque;

use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Events\Dispatcher;

class PictureBuilder {

    /**
     * An HTML builder to parse html attributes
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    protected $builder;

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;

    /**
     * A dispatcher to fire events with
     *
     * @var \Illuminate\Events\Dispatcher
     */
    protected $event;

    /**
     * Initialize the instance
     *
     * @param  \Illuminate\Html\HtmlBuilder     $builder
     * @param  \Illuminate\Routing\UrlGenerator $url
     * @param  \Illuminate\Events\Dispatcher    $event
     * @return void
     */
    public function __construct(HtmlBuilder $builder = null, UrlGenerator $url = null, Dispatcher $event)
    {
        $this->builder  = $builder;
        $this->url      = $url;
        $this->event    = $event;
    }

    /**
     * Return a resised image
     *
     * @param  string  $url
     * @param  string  $size
     * @param  string  $alt
     * @param  array   $attributes
     * @param  boolean $secure
     * @return string
     */
    public function make($url, $size = 'full', $alt = null, $attributes = [], $secure = false)
    {
        $attributes['alt'] = $alt;

        $url = $this->getResizedUrl($url, $size);

        return '<img src="'.$this->url->asset($url, $secure).'"'.$this->builder->attributes($attributes).'>';
    }

    /**
     * Send an event to get the image resized somewhere else
     *
     * @param  string $url
     * @param  string $size
     * @return string
     */
    protected function getResizedUrl($url, $size)
    {
        $paths = $this->event->fire('picturesque::resize', [$this->realPath($url), $size]);

        if(count($paths) && ! empty($paths[0]))
        {
            $url = $this->publicPath($paths[0]);
        }

        return $url;
    }

    /**
     * Return the path for a file relative to Laravel's public dir
     *
     * @param  string $path
     * @return string
     */
    protected function publicPath($path)
    {
        // To create a "real" path suitable for the asset function
        // we must first remove the path to the public path directory
        $pos = strpos($path,public_path());
        if($pos === 0){
            return substr_replace($path, '', $pos, strlen(public_path()));
        }
        return $path;
    }

    /**
     * Return the path from the system root for a file
     *
     * @param  string $path
     * @return string
     */
    protected function realPath($url)
    {
        $path = \realpath(public_path() . '/' . ltrim($url, '/'));
        if( ! $path)
        {
            throw new Exceptions\WrongPathException("File {$url} not found. The path url provided must be relative to Laravel's public path.");
        }

        return $path;
    }
}
