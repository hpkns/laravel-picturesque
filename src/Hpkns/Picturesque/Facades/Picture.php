<?php namespace Fideloper\Example\Facades;

class Picture extends \Illuminate\Support\Facades\Facade {
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'picturesque.builder'; }
}
