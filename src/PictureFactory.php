<?php

namespace Hpkns\Picturesque;

class PictureFactory
{
    /**
     * @var \Hpkns\Picturesque\FormatRepository
     */
    protected $formats;

    /**
     * @var \Hpkns\Picturesque\Cache
     */
    protected $cache;

    /**
     * @var \Hpkns\Picturesque\FilterRepository
     */
    protected $filters;

    /**
     * @var \Hpkns\Picturesque\Resizer
     */
    protected $resizer;

    /**
     * Initiliaze the factory.
     *
     * @param Hpkns\Picturesque\FormatRepository $formats
     * @param Hpkns\Picturesque\Cache            $cache
     * @param Hpkns\Picturesque\FilterRepository $filters
     * @param Hpkns\Picturesque\Resizer          $resizer
     */
    public function __construct(FormatRepository $formats, Cache $cache, FilterRepository $filters, Resizer $resizer)
    {
        $this->formats = $formats;
        $this->cache = $cache;
        $this->filters = $filters;
        $this->resizer = $resizer;
    }

    /**
     * Create a new Picturesque Picture.
     *
     * @param  string $path
     * @param  array  $attributes
     * @return \Hpkns\Picturesque\Picture
     */
    public function picture($path, array $attributes = [])
    {
        return new Picture($path, $attributes, $this->formats, $this->cache, $this->resizer);
    }

    /**
     * Create a new image
     *
     * @param  string $path
     * @return \Hpkns\Picturesque\Image
     */
    public function image($path)
    {
        return new Image($path, $this->filters);
    }

    /**
     * Return the format repository.
     *
     * @return \Hpkns\Picturesque\FormatRepository
     */
    public function formats()
    {
        return $this->formats;
    }

    /**
     * Return the filters repository.
     *
     * @return \Hpkns\Picturesque\FilterRepository
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * Return the picture cache.
     *
     * @return \Hpkns\Picturesque\Cache
     */
    public function cache()
    {
        return $this->cache;
    }
}
