<?php

namespace Hpkns\Picturesque;

use Intervention\Image\ImageManager;
use Intervention\Image\Image as InterventionImage;

class Image
{
    /**
     * The path to the real file.
     *
     * @var string
     */
    public $path;

    /**
     * The format.
     *
     * @var \Hpkns\Picturesque\Format
     */
    public $format;

    /**
     * The underlying image.
     *
     * @var Intervention\Image\Image
     */
    protected $image;

    /**
     * The filter repository.
     *
     * @var \Hpkns\Picturesque\FilterRepository
     */
    protected $filters;

    /**
     * Initialize the instance.
     *
     * @var Intervention\Image\ImageManager
     */
    public function __construct($image, FilterRepository $filters)
    {
        if (! $image instanceof InterventionImage) {
            $image = app(ImageManager::class)->make($image);
        }

        $this->image = $image;
        $this->filters = $filters ?: app('picturesque.filters');
    }

    /**
     * Process the image according to a format.
     *
     * @param  \Hpkns\Picturesque\Format $format
     * @return $this
     */
    public function process($format)
    {
        if (! $format->no_resize) {
            $this->resize($format->width, $format->height, $format->crop);
        }

        foreach ($format->filters as $name => $options) {
            if (is_int($name)) {
                if (strpos($options, ':') === false) {
                    $name = $options;
                    $options = [];
                } else {
                    list($name, $value) = explode(':', $options);
                    $options = compact('value');
                }
            }

            $filter = $this->filters->get($name);
            app()->call($filter, ['image' => $this] + $options, 'handle');
        }

        return $this;
    }

    /**
     * Resize the shit!
     *
     * @param  int  $width
     * @param  int  $height
     * @param  bool $crop
     * @return $this
     */
    public function resize($width, $height, $crop = false)
    {
        if ($crop) {
            $this->image->fit($width, $height);
        } else {
            $this->image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        return $this;
    }

    /**
     * Outsource to the underlying class.
     *
     * @param  string $method
     * @param  array  $attributes
     * @return mixed
     */
    public function __call($method, $attributes)
    {
        return $this->image->{$method}(...$attributes);
    }

    /**
     * Outsource to the underlying class.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->image->{$key};
    }
}
