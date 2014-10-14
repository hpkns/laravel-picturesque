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
     * The name of the default size format
     *
     * @var string
     */
    protected $defaultSizeName;

    /**
     * Size format
     *
     * @var string
     */
    protected $sizeFormat = "%sx%s%s";

    /**
     * Default size hash
     *
     * @var string
     */
    protected $defaultSize = [
        'width'   => null,
        'height'  => null,
        'crop'    => false,
        'filters' => [],
    ];

    /**
     * Initialise the instance
     *
     * @param  \Intervention\Image\ImageManager $manager
     * @param  array  $format
     * @param  string $cache
     * @return void
     */
    public function __construct(ImageManager $manager = null, $sizes = [], $cache = null, $defaultSizeName = '')
    {
        $this->manager = $manager ?: new ImageManager;
        $this->sizes = $sizes;
        $this->cachePath = $cache;
        $this->defaultSizeName = $defaultSizeName;
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
            $size_name = $size != 'default' ? $size : $this->getDefaultSizeName();
            $size = $this->getNamedSize($size_name);
        }
        else
        {
            $size_name = $this->getSizeName($size);
        }

        if(empty($size) || $size_name == 'full')
        {
            return $path;
        }

        $size = $this->regularizeSize($size);

        $output = $this->getOutputPath($path, $size_name);

        if( ! file_exists($output) || filemtime($path) > filemtime($output) || $force)
        {
            $this->resize($path, $size, $output);
        }

        return $output;
    }

    public function regularizeSize($size)
    {
        if( ! isset($size['width']) && ! isset($size['height']))
        {
            throw new Exceptions\FormatDimentionMissing;
        }

        if(in_array('crop', $size) || in_array('cropped', $size))
        {
            $size['crop'] = true;
        }

        // Since we extract the return of this function we must make sure
        // that keys present in the array won't cause a collision with a function
        // latter on. To prevent this we make sure that the keys present in
        // the returned array are limited to the same that are present in the default
        // array, hence the array_intersect_key.
        return array_intersect_key(
            array_merge(
                $this->defaultSize,
                $size
            ),
            $this->defaultSize
        );
    }

    /**
     * Return the output path
     *
     * @param  string $file
     * @param  string  $size_name
     * @return string
     */
    public function getOutputPath($file, $size_name)
    {
        $infos = pathinfo($file);
        $prefix = '';

        if( ! empty($this->cachePath))
        {
            $folder = $this->cachePath;
            $prefix = md5($file) . '-';
            if(strpos($folder, public_path()) !== 0)
            {
                $folder = public_path() . '/' . ltrim($folder, '/');
            }
        }
        else
        {
            $folder = $infos['dirname'];
        }

        return "{$folder}/{$prefix}{$infos['filename']}-{$size_name}.{$infos['extension']}";
    }

    /**
     * Return the default size name
     *
     * @return string
     */
    public function getDefaultSizeName()
    {
        if(empty($this->defaultSizeName))
        {
            if(count($this->sizes))
            {
                return array_keys($this->sizes)[0];
            }

            return false;
        }

        return $this->defaultSizeName;
    }

    /**
     * Return the registered size from its name
     *
     * @param  string $size
     * @return array
     */
    public function getNamedSize($size)
    {
        if(isset($this->sizes[$size]))
        {
            return $this->sizes[$size];
        }

        throw new Exceptions\UnknownFormatException("The format $size does not exists.");
    }

    /**
     * Create the name for a size when provided one as an array
     *
     * @param  array $size
     * @return string
     */
    public function getSizeName(array $size = [])
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
    public function resize($input, array $format, $output = null)
    {
        extract($format);

        $img = $this->manager->make($input);

        if($crop)
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
