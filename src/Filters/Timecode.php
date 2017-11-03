<?php

namespace Hpkns\Picturesque\Filters;

use Hpkns\Picturesque\Image;
use Intervention\Image\ImageManager;
use Hpkns\Picturesque\Support\Contracts\Filter;

class Timecode implements Filter
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
    public function handle(Image $image, Callable $font = null)
    {
        $image->text(date('Y-m-d H:i:s'), 10, $image->height() - 10, $font ?: function ($font) {
            $font->size(24);
            $font->file('/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf');
            $font->color('#FFFFFF');
        });
    }
}
