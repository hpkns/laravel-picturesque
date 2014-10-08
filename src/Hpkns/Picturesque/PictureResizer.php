<?php namespace Hpkns\Picturesque;

use Intervention\Image\ImageManager;

class PictureResizer implements Contracts\PictureResizerContract {

    /**
     *  A list of sizes known to the instance
     *
     * @var array
     */
    protected $sizes;

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
     * Size format
     *
     * @var string
     */
    protected $sizeFormat = "%sx%s%s";

    /**
     * Initialise the instance
     *
     * @param  \Intervention\Image\ImageManager $manager
     * @param  array  $format
     * @param  string $cache
     * @return void
     */
    public function __construct(ImageManager $manager = null, $sizes = [], $cache = null)
    {
        $this->manager = $manager ?: new ImageManager;
        $this->sizes = $sizes;
        $this->cachePath = $cache;
    }

    /**
     * Return a resized version of the picture
     *
     * @param  string  $path
     * @param  array   $size
     * @param  boolean $force
     * @return string
     */
    public function getResized($path, $size = [], $force = false)
    {
        if(is_string($size))
        {
            $size_name = $size;
            $size = $this->getNamedSize($size);
        }
        else
        {
            $size_name = $this->getSizeName($size);
        }

        if(empty($size) || $size_name == 'full')
        {
            return $path;
        }

        $output = $this->getOutputPath($path, $size_name);

        if( ! file_exists($output) || filemtime($path) > filemtime($output) || $force)
        {
            $this->resize($path, $size, $output);
        }

        return $output;
    }

    /**
     * Return the output path
     *
     * @param  string $file
     * @param  string  $size_name
     * @return string
     */
    protected function getOutputPath($file, $size_name)
    {
        $infos = pathinfo($file);
        $prefix = '';

        if( ! empty($this->cachePath))
        {
            $folder = $this->cachePath;
            $prefix = md5($file) . '-';
        }
        else
        {
            $folder = $infos['dirname'];
        }

        return "{$folder}/{$prefix}{$infos['filename']}-{$size_name}.{$infos['extension']}";
    }

    /**
     * Return the registered size from its name
     *
     * @param  string $size
     * @return array
     */
    protected function getNamedSize($size)
    {
        if(isset($this->sizes[$size]))
        {
            return $this->sizes[$size];
        }
    }

    /**
     * Create the name for a size when provided one as an array
     *
     * @param  array $size
     * @return string
     */
    protected function getSizeName(array $size = [])
    {
        return sprintf($this->sizeFormat,
            (isset($size['width']) ? $size['width'] : '-'),
            (isset($size['height']) ? $size['height'] : '-'),
            (isset($size['crop']) && $size['crop'] ? '-cropped' : '')
        );
    }

    /**
     * Resizes an image to the desired format
     *
     * @param  string $input
     * @param  array  $format
     * @param  string $output
     * @return string
     */
    protected function resize($input, array $format, $output = null)
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
    }
}
