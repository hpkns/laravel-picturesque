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
        $this->app['events']->listen('picturesque::resize', 'picturesque.resizer@resize');
    }
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bindShared('picturesque.builder', function($app){
            return new PictureBuilder($app['html'], $app['url'], $app['events']);
        });
        $this->app->bindShared('picturesque.resizer', function($app){
            return new PictureResizer(new \Intervention\Image\ImageManager);
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return []; //['picturesque.resizer', 'picturesque.builder'];
	}
}
