<?php namespace Hpkns\Picturesque;

use Illuminate\Html\HtmlBuilder as Builder;

class PictureBuilder {

    /**
     * A resizing tool
     *
     * @var \Hpkns\Picturesque\ImageResizer
     */
    protected $resizer;

    /**
     * An HTML builder to parse html attributes
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    protected $builder;

    /**
     * Initialize the instance
     *
     * @param  \Hpkns\Picturesque\ImageResizer $resizer
     * @param  \Illuminate\Html\HtmlBuilder    $builder
     * @return \Hpkns\Picturesque\Picture
     */
    public function __construct(Resizer $resizer = null, Builder $builder = null)
    {
        $this->resizer = $resizer;
        $this->builder = $builder;
    }

    /**
     * Create new instances new pictures
     *
     * @param  string $path
     * @param  string $al
     * @return \Hpkns\Picturesque\Picture
     */
    public function create($path, $alt = '')
    {
        return new Picture($path, $alt, $this->resizer, $this->builder);
    }
}
