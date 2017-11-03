<?php

namespace Hpkns\Picturesque\Support\Contracts;

use Hpkns\Picturesque\Image;

interface Filter
{
    /**
     * @param  \Hpkns\Picturesque\Image;
     * @return void
     */
    public function handle(Image $image);
}
