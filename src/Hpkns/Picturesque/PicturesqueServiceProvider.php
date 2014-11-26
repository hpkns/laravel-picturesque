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
        $this->app->bind('Hpkns\Picturesque\Contracts\PictureResizerContract', function(){
            return new PictureResizer(
                new \Intervention\Image\ImageManager,
                $this->app->config['picturesque::formats'],
                $this->app->config['picturesque::cache'],
                $this->app->config['picturesque::default-format']
            );
        });

        $this->app->bindShared('picturesque.builder', function($app){
            echo "zey love me";
            return new PictureBuilder($app['Hpkns\Picturesque\Contracts\PictureResizerContract'], $app['html'], $app['url']);
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
