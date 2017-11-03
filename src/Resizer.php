<?php

namespace Hpkns\Picturesque;

use Hpkns\Picturesque\Support\Contracts\ResizePromise;

class Resizer
{
    /**
     * @param  string $timing
     * @param  string $storage
     */
    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    /**
     * Apply a format resize and return the resulting image.
     *
     * @param  string $path
     * @param  string $format
     * @return \Hpkns\Picturesque\Image
     */
    public function resizeOrCreatePromise($path, $format)
    {
        if (
            (config('picturesque.timing') === 'async' || $format->force_async)
            && ! $format->data_url
            && ! $format->force_sync
        ) {
            return app(ResizePromise::class)->create(compact('path', 'format'));
        } else {
            return $this->resize($path, $format);
        }
    }

    /**
     *
     *
     */
    public function resize($path, $format)
    {
        return app('picturesque')->image($path)->process($format);
    }
}
