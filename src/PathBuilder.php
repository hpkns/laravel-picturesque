<?php

namespace Hpkns\Picturesque;

use LogicException;
use Hpkns\Picturesque\Events\ResizedPathCreated;

class PathBuilder
{
    /**
     * Initialize the path builder.
     *
     * @param string $cache_path
     */
    public function __construct($cache_path = '', $path_base)
    {
        $this->cachePath = rtrim($cache_path, '/');
        $this->pathBase = $path_base;
    }

    /**
     * Find the path a picture file.
     *
     * @param  string $path
     * @return string
     */
    protected function getPicturePath($path)
    {
        if ($this->pathBase == 'storage') {
            $base = storage_path('app');
        } elseif ($this->pathBase == 'public') {
            $base = public_path();
        } else {
            return $path;
        }

        return
            starts_with($path, $base)
            ? realpath($path)
            : realpath(rtrim($base, '/') . DIRECTORY_SEPARATOR . ltrim($path, '/'));
    }

    protected function getCachedPath($path, $format, $directory, $prefix = '')
    {
        $info = pathinfo($path);

        return implode('', [
            rtrim($directory, '/') , DIRECTORY_SEPARATOR , $prefix ,
            $info['filename'], '-', $format->name, '.', $info['extension']
        ]);
    }

    /**
     * Get the correct resized path.
     *
     * @param  string $path
     * @param  \Hpkns\Picturesque\Format $format
     */
    public function getResizedPath($path, Format $format)
    {
        $path = $this->getPicturePath($path);

        if (! $path || !is_file($path)) {
            return;
        }

        if ($this->cachePath) {
            $resized_path = $this->getCachedPath($path, $format, $this->cachePath, md5(dirname($path)));
        } else {
            $resized_path = $this->getCachedPath($path, $format, dirname($path));
        }

        if (! file_exists($resized_path) ||  filemtime($resized_path) < filemtime($path)) {
            event(new ResizedPathCreated($path, $resized_path, $format));
        }

        return $resized_path;
    }

    /**
     * Return the url to a file
     *
     */
    public function getResizedUrl($path, Format $format, $secure = false)
    {
        $path = $this->getResizedPath($path, $format);

        if ($path) {
            return asset(str_replace(public_path(), '', $path), $secure);
        }
    }
}
