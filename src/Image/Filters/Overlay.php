<?php

namespace Hpkns\Picturesque\Image\Filters;

use Hpkns\Picturesque\Image\Image;
use Intervention\Image\ImageManager;

class Overlay extends Filter
{
    /**
     * The filter configuration.
     *
     * @var array
     */
    protected $options = [
        'scale'     => 0.5,
        'position'  => 'center',
        'x'         => 0,
        'y'         => 0
    ];

    /**
     * Apply the filter to the image.
     *
     * @param \Hpkns\Picturesque\Image\Image
     * @return void
     */
    public function handle(Image $image, $options)
    {
        $overlay = app(ImageManager::class)->make($options['value']);

        if ($options['scale']) {
            $size = min($image->width(), $image->height()) * $options['scale'];
            $overlay->resize($size, $size);
        }

        $image->insert($overlay, $options['position'], $options['x'], $options['y']);
    }
}
