<?php

namespace Hpkns\Picturesque;

use Illuminate\Support\ServiceProvider;

class PicturesqueServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
                __DIR__.'/../config/default.php' => config_path('picturesque.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(
            'Hpkns\Picturesque\Contracts\PictureResizerContract',
            'Hpkns\Picturesque\PictureResizer'
        );

        $this->app->singleton('picturesque.builder', function ($app) {
            $resizer = $app['Hpkns\Picturesque\Contracts\PictureResizerContract'];
            $repository = (new FormatRepository($app['config']['picturesque.cache'], $app['config']['picturesque.default_format']))
                ->addFormats($app['config']['picturesque.formats']);

            return new PictureBuilder($resizer, $repository, $app['config']['picturesque.cache']);
        });

        $this->app->alias('picturesque.builder', 'Hpkns\Picturesque\PictureBuilder');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['picturesque.builder'];
    }
}
