<?php

namespace Hpkns\Picturesque;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Intervention\Image\ImageManager;

class PicturesqueServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publish();

        if (config('picturesque.timing', 'async') == 'async') {
            $router->group(['middleware'=> 'web'], function()  use ($router){
                $router->get('picturesque/{resizeable}', Http\ResizeableController::class . '@resize')
                    ->name('picturesque.resize');
            });
        }
    }

    /**
     * Publishes the config and migrations.
     *
     * @return void
     */
    public function publish()
    {
        $this->publishes([
            __DIR__.'/../config/picturesque.php' => config_path('picturesque.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('picturesque.formats', function() {
            $formats = new Formats\FormatRepository();
            $formats->addFormats(config('picturesque.formats', []));

            return $formats;
        });

        $this->app->singleton('picturesque.resizer', function($app) {
            $resizer = new Image\ImageResizer(config('picturesque.quality', 80), $app->make(ImageManager::class));
            $resizer->registerFilters(config('picturesque.filters', []));

            return $resizer;
        });

        $this->app->singleton('picturesque.paths', function($app) {
            return new Paths\PathBuilder(config('picturesque.cache'), $app['picturesque.resizer']);
        });
    }
}
