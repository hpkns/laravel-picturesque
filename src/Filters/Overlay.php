<?php

namespace Hpkns\Picturesque\Filters;

use Intervention\Image\ImageManager;
use Hpkns\Picturesque\Image;
use Hpkns\Picturesque\Support\Contracts\Filter;

class Overlay implements Filter
{
    /**
     * Intervention image manager to create picture.
     *
     * @param Intervention\Image\ImageManager;
     */
    protected $imageManager;

    /**
     * Initiliaze the filter.
     *
     * @param Intervention\Image\ImageManager;
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Apply the filter to the image.
     *
     * @param \Hpkns\Picturesque\Image\Image
     * @return void
     */
    public function handle(Image $image/*, $overlay, $position = 'center', $x = 0, $y = 0, $scale = 0.5*/)
    {
        /**
        $overlay = $this->imageManager->make($overlay);

        if ($scale) {
            $size = min($image->width(), $image->height()) * $scale;
            $overlay->resize($size, $size);
        }

        $image->insert($overlay, $position, $x, $y);
        **/
    }
}
