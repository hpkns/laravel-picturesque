<?php

namespace Hpkns\Picturesque;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Intervention\Image\ImageManager;
use Hpkns\Picturesque\Support\Contracts\ResizePromise;
use Hpkns\Picturesque\Resize\EloquentResizePromise;
use Hpkns\Picturesque\Resize\RedisResizePromise;

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

        $router->group(['middleware'=> 'web'], function()  use ($router){
            $router->get('picturesque/{promise}', Http\PromiseController::class . '@fullfill')
                ->name('picturesque.promise');
        });

        $router->bind('promise', function($slug) {
            return(app(ResizePromise::class))->find($slug);
        });
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
        switch(config('picturesque.storage', 'eloquent')) {
        case 'redis':
            $this->app->bind(ResizePromise::class, EloquentResizePromise::class);
        default:
            $this->app->bind(ResizePromise::class, RedisResizePromise::class);
        }

        $this->app->singleton('picturesque.formats', function() {
            $formats = new FormatRepository();
            foreach (config('picturesque.formats', []) as $name => $format) {
                $formats->add($name, $format);
            }
            return $formats;
        });
        $this->app->bind(FormatRepository::class, 'picturesque.formats');

        $this->app->singleton('picturesque.filters', function ($app) {
            $filters = new FilterRepository;
            foreach (config('picturesque.filters', []) as $name => $class) {
                $filters->registerFilter($name, $class);
            }
            return $filters;
        });
        $this->app->bind(FilterRepository::class, 'picturesque.filters');

        $this->app->singleton('picturesque.resizer', function ($app) {
            return new Resizer(config('picturesque.timing'), config('picturesque.store'));
        });
        $this->app->bind(Resizer::class, 'picturesque.resizer');

        $this->app->singleton('picturesque.cache', function ($app) {
            return new Cache(config('picturesque.cache.disk'), config('picturesque.cache.prefix'));
        });
        $this->app->bind(Cache::class, 'picturesque.cache');

        $this->app->singleton('picturesque', function ($app) {
            return new PictureFactory(
                $app['picturesque.formats'],
                $app['picturesque.cache'],
                $app['picturesque.filters'],
                $app['picturesque.resizer']
            );
        });
        $this->app->bind(PictureFactory::class, 'picturesque');
    }
}
