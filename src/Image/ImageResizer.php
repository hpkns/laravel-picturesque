<?php

namespace Hpkns\Picturesque\Image;

use Hpkns\Picturesque\Events\PictureResized;
use Hpkns\Picturesque\Database\Resizeable;

class ImageResizer
{
    /**
     * A list of filters.
     *
     * @var array $filters
     */
    protected $filters = [];

    /**
     * A list of filter objects.
     *
     * @var array $filterModels
     */
    protected $filterModels = [];

    /**
     * Handle the resizing of a picture.
     *
     * @param  string                           $path
     * @param  string                           $resized_path
     * @param  Hpkns\Picturesque\Formats\Format $format
     * @return string
     */
    public function handle($path, $resized_path, $format)
    {
        if (! $this->fileExists($path)) {
            return;
        }

        if ($this->fileExists($resized_path)) {
            unlink($resized_path);
        }

        if (config('picturesque.timing', 'async') == 'sync' || !$format->option('async')) {
            return $this->resize($path, $resized_path, $format);
        } else {
            return $this->schedule($path, $resized_path, $format);
        }
    }

    protected function fileExists($path)
    {
        return file_exists($path) && is_file($path);
    }

    /**
     * Resize a picture.
     *
     * @param  string                               $from
     * @param  string                               $to
     * @param  \Hpkns\PictureResized\Formats\Format $format
     * @return string
     */
    public function resize($from, $to, $format)
    {
        if (! $this->fileExists($from)) {
            return ;
        }

        $i = new Image($from);

        $i->resize($format->width, $format->height, $format->crop);

        foreach ($format->option('filters', []) as $key => $name) {
            $this->applyFilter($i, $key, $name);
        }

        $i->save($to , array_get($format->options, 'quality', 80));

        event(new PictureResized($i, $from, $to, $format));

        return $to ;
    }

    /**
     * Store the image in the database to resize later.
     *
     * @param  string                               $from
     * @param  string                               $to
     * @param  \Hpkns\PictureResized\Formats\Format $format
     * @return string
     */
    public function schedule($path, $resized_path, $format)
    {
        $model = Resizeable::updateOrCreate(compact('resized_path'), compact('path', 'resized_path', 'format'));

        return route('picturesque.resize', $model);
    }

    /**
     *
     * @param  \Hpkns\Picturesque\Image\Image $image
     * @param  mixed                          $key
     * @param  mixed                          $name
     * @return \Hpkns\Picturesque\Image\Filters\Filter
     */
    protected function applyFilter(Image $image, $key, $name)
    {
        if (is_string($key) && is_array($name)) {
            $options = $name;
            $name = $key;

        } elseif (strpos($name, ':') !== false) {
            list($name, $value) = explode(':', $name);
            $options = ['value' => $value];
        } else {
            $options = [];
        }

        return $this->getFilter($name)->fire($image, $options);
    }

    /**
     * Register filters.
     *
     * @param  array $args
     * @return void
     */
    public function registerFilters(...$args)
    {
        if (count($args) > 1 && is_string($args[0])) {
            return $this->registerFilters([$args[0] => $args[1]]);
        }

        foreach ($args[0] as $name => $binding) {
            $this->filters[$name] = $binding;
        }
    }

    /**
     * Return a filter.
     *
     * @param  string $name
     * @return \Hpkns\Picturesque\Image\Filters\Filter
     */
    public function getFilter($name)
    {
        if (! array_key_exists($name, $this->filterModels)) {
            $this->filterModels[$name] = app($this->filters[$name]);
        }

        return $this->filterModels[$name];
    }
}
