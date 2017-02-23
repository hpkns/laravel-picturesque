<?php

namespace Hpkns\Picturesque;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;

class PicturesqueServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot(Dispatcher $events, Router $router)
    {
        $router->get('/images/cache/{path}', ['as' => 'picturesque.resize', 'uses' => Http\PictureController::class . '@showResized'])
            ->where('path', '.*');

        $events->listen(Events\ResizedPathCreated::class, Listeners\ResizePicture::class);

        $this->publishes([
            __DIR__.'/../config/picturesque.php' => config_path('picturesque.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('picturesque.formats', function() {
            return (new FormatRepository)->addFormats(config('picturesque.formats'));
        });

        $this->app->singleton('picturesque.paths', function() {
            return new PathBuilder(config('picturesque.cache'), config('picturesque.path_base'));
        });

        $this->app->singleton('picturesque.resizer', function($app) {
            return new PictureResizer(config('picturesque.quality', 80), $app->make(ImageManager::class));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['picturesque.formats', 'picturesque.paths'];
    }
}

