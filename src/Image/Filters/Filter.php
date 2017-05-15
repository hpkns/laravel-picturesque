<?php

namespace Hpkns\Picturesque\Image\Filters;

use Hpkns\Picturesque\Image\Image;

class Filter
{
    /**
     * The filter configuration.
     *
     * @var array
     */
    protected $options = [
        'value' => null
    ];

    /**
     * Prep for the handling.
     *
     * @param  \Hpkns\Picturesque\Image\Image $image
     * @param  array                          $options
     * @return void
     */
    public function fire(Image $image, $options = [])
    {
        return $this->handle($image, array_merge($this->options, $options));
    }

    /**
     * Apply the filter to the image.
     *
     * @param  \Hpkns\Picturesque\Image\Image $image
     * @param  array                          $options
     * @return void
     */
    public function handle(Image $image, $options)
    {
        //
    }

    // if (isset($format->options['resize_fill']) && ($format->width && $format->height)) {
    //     if (true || $format->options['resize_fill'] == 'checker') {
    //         $background = $this->manager->make(__DIR__ . '/../resources/checker.png');
    //         $background->crop($format->width, $format->height);
    //         $background->insert($p, 'center');
    //         $p = $background;
    //     } else {
    //         $p->resizeCanvas($format->width, $format->height, 'center', false, $format->options['resize_fill']);
    //     }
    // }

    // if (isset($format->options['overlay'])) {
    //     $overlay = $this->manager->make($format->options['overlay']);
    //     $p->insert($overlay, 'center');
    // }
}
