<?php

namespace Hpkns\Picturesque;

use Storage;

class Cache
{
    /**
     *
     */
    public function __construct($disk)
    {
        $this->disk = $disk;
    }

    /**
     * Does the cached version exist.
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return Storage::disk($this->disk)->exists($name);
    }

    public function newerThan($name, $ftime)
    {
        return Storage::disk($this->disk)->lastModified($name) > $ftime;
    }

    public function getUrl($name)
    {
        return Storage::disk($this->disk)->url($name);
    }

    public function get($name)
    {

        return Storage::disk($this->disk)->get($name);
    }

    /**
     * Return the name the file will have once cached.
     *
     * @param  string                    $path
     * @param  \Hpkns\Picturesque\Format $format
     * @return string
     */
    public function getName($path, $format)
    {
        if (file_exists($path)) {
            return md5(realpath($path))
                . '-' . $format->name
                . '.' . pathinfo($path, PATHINFO_EXTENSION);
        }
    }

    /**
     *
     *
     */
    public function save($image, $name)
    {
        Storage::disk($this->disk)->put($name, $image->encode());

        return $this->getUrl($name);
    }
}

