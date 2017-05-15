<?php

namespace Hpkns\Picturesque\Image\Filters;

use Hpkns\Picturesque\Image\Image;

class Darken extends Filter
{
    /**
     *
     * @param  Hpkns\Picturesque\Image\Image $image
     * @param  ...mixed $options
     * @return void
     */
    public function handle(Image $image, $options)
    {
        $image->fill([0, 0, 0, (int)$options['value'] / 100]);
    }
}
