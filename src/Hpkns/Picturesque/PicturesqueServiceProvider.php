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
                __DIR__.'/../../config/default.php' => config_path('picturesque.php')
        ], 'config');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bind('Hpkns\Picturesque\Contracts\PictureResizerContract', function(){
            $config = config('picturesque');
            return new PictureResizer(
                new \Intervention\Image\ImageManager,
                $config['formats'],
                $config['cache'],
                $config['default-format']
            );
        });

        $this->app->singleton('picturesque.builder', function($app){
            return new PictureBuilder(
                $app['Hpkns\Picturesque\Contracts\PictureResizerContract'],
                $app['url']
            );
        });

        $this->app->alias('Hpkns\Picturesque\PictureBuilder', 'picturesque.builder');
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
