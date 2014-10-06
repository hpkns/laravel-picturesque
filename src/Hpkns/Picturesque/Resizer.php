<?php namespace Hpkns\Picturesque;

use Intervention\Image\ImageManager;

class Resizer {

    /**
     *  A list of formats known to the instance
     *
     * @var array
     */
    protected $formats;

    /**
     * The path to the cache folder from the public directory
     *
     * @var string
     */
    protected $cachePath;

    /**
     * A manager to resize images with
     *
     * @var \Intervention\Image\ImageManager
     */
    protected $manager;

    /**
     * Initialise the instance
     *
     * @param  \Intervention\Image\ImageManager $manager
     * @param  array  $format
     * @param  string $cache
     * @return void
     */
    public function __construct(ImageManager $manager, $formats = [], $cache = '/images/cache')
    {
        $this->manager = $manager;
        $this->formats = $formats;
        $this->cachePath = $cache;
    }

    /**
     * Return the path to a cached version if it exists
     *
     * @param  string $path
     * @param  string $format
     * @return mixed
     */
    public function getPath($path, $format = 'full')
    {
        if($format == 'full')
        {
            return $path;
        }

        if($cached = $this->getCached($path, $format))
        {
            return $cached;
        }

        return $this->resize(public_path() . $path, $this->formats[$format], public_path() . $this->getCachedPath($path, $format));
    }

    /**
     * Return the path to a cached version if it exists
     *
     * @param  string $path
     * @param  string $format
     * @return mixed
     */
    protected function getCached($path, $format)
    {
        $cached = $this->getCachedPath($path, $format);
        if(file_exists(public_path() . $cached ))
        {
            return $cached;
        }
    }

    /**
     * Return the path to the cached version
     *
     * @param  string $path
     * @param  string $format
     * @return mixed
     */
    protected function getCachedPath($path, $format)
    {
        $extenstion = pathinfo(public_path() . "$path", PATHINFO_EXTENSION);
        return "{$this->cachePath}/". md5("{$path}-{$format}") . ".{$extenstion}";
    }

    /**
     * Return true if the format string correspond to a format know to this instance
     *
     * @param  string $format
     * @return boolean
     */
    public function formatExists($format)
    {
        return in_array($format, array_keys($this->formats));
    }


    /**
     * Resizes an image to the desired format
     *
     * @param  string $input
     * @param  array  $format
     * @param  string $output
     * @return string
     */
    public function resize ($input, array $format, $output = null)
    {
        $img = $this->manager->make($input);

        extract($format);
        if(isset($crop) && $crop)
        {
            $img->fit($width, $height);
        }
        else
        {
            $img->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });;
        }

        $img->save($output);

        return str_replace(public_path(), '', $output);
    }
}
