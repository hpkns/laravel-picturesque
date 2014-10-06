<?php namespace Hpkns\Picturesque;

use Illuminate\Support\ServiceProvider;

class PicturesqueServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    public function boot()
    {
        $this->package('hpkns/picturesque');
    }
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bindShared('picturesque.resizer', function($app){
            return new ImageResizer(
                new \Intervention\Image\ImageManager,
                $app->config['picturesque::sizes'],
                $app->config['picturesque::cache']
            );
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}