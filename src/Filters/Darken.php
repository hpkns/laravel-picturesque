<?php

namespace Hpkns\Picturesque\Filters;

use Hpkns\Picturesque\Image;
use Hpkns\Picturesque\Support\Contracts\Filter;

class Darken implements Filter
{
    /**
     *
     * @param  Hpkns\Picturesque\Image\Image $image
     * @return void
     */
    public function handle(Image $image, $value = 50)
    {
        $image->fill([0, 0, 0, (int)$value / 100]);
    }
}
