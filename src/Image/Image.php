<?php

namespace Hpkns\Picturesque\Image;

use Intervention\Image\ImageManager;
use Intervention\Image\Image as BaseImage;

class Image
{
    /**
     * The underlying image.
     *
     * @var Intervention\Image\Image
     */
    protected $image;

    /**
     * Initialize the instance.
     *
     * @var Intervention\Image\ImageManager
     */
    public function __construct($path, ImageManager $manager = null)
    {
        $manager = $manager ?: app(ImageManager::class);

        $this->image = $manager->make($path);
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
