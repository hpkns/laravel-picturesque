<?php

namespace Hpkns\Picturesque\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Picturesque extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'picturesque'; }
}
