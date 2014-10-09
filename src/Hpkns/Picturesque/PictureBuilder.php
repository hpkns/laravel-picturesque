<?php namespace Hpkns\Picturesque;

use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;
use Hpkns\Picturesque\Contracts\PictureResizerContract as Resizer;

class PictureBuilder {

    /**
     * A resizer
     *
     * @var Contracts\PictureResizerContract
     */
    protected $resizer;

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
     * Initialize the instance
     *
     * @param  \Contracts\PictureResizerContract $resizer
     * @param  \Illuminate\Html\HtmlBuilder      $builder
     * @param  \Illuminate\Routing\UrlGenerator  $url
     * @return void
     */
    public function __construct(Resizer $resizer = null, HtmlBuilder $builder = null, UrlGenerator $url = null)
    {
        $this->resizer  = $resizer;
        $this->builder  = $builder;
        $this->url      = $url;
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

        $url = $this->getResized($url, $size);

        return '<img src="'.$this->url->asset($url, $secure).'"'.$this->builder->attributes($attributes).'>';
    }

    /**
     * Users the resizer to resize the image
     *
     * @param  string $url
     * @param  string $size
     * @return string
     */
    protected function getResized($url, &$size)
    {
        return $this->publicPath($this->resizer->getResized($this->realPath($url), $size));
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
        $pos = strpos($path, public_path());
        if($pos !== 0){
            throw new Exceptions\NotInPublicPathException("The resized file is {$path} not in Laravel public_path. Well, it should be!");
        }
        return ltrim(substr_replace($path, '', $pos, strlen(public_path())), '/');

    }

    /**
     * Return the path from the system root for a file
     *
     * @param  string $path
     * @return string
     */
    protected function realPath($url)
    {
        $path = realpath(public_path() . '/' . ltrim($url, '/'));
        if( ! $path)
        {
            throw new Exceptions\WrongPathException("File {$url} not found. The path url provided must be relative to Laravel's public path.");
        }

        return $path;
    }
}
