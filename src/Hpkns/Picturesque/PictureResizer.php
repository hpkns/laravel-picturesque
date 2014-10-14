<?php namespace Hpkns\Picturesque;

use Intervention\Image\ImageManager;

class PictureResizer implements Contracts\PictureResizerContract {

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
     * The name of the default format format
     *
     * @var string
     */
    protected $defaultFormat;

    /**
     * Format sprintf model
     *
     * @var string
     */
    protected $formatNameModel = "%sx%s%s";

    /**
     * Default format hash
     *
     * @var string
     */
    protected $defaultFormatValues = [
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
    public function __construct(ImageManager $manager = null, $formats = [], $cache = null, $defaultFormat = '')
    {
        $this->manager = $manager ?: new ImageManager;
        $this->formats = $formats;
        $this->cachePath = $cache;
        $this->defaultFormat = $defaultFormat;
    }

    /**
     * Return a resized version of the picture
     *
     * @param  string  $path
     * @param  array   $format
     * @param  boolean $force
     * @return string
     */
    public function getResized($path, $format = [], $force = false)
    {
        if(is_string($format))
        {
            $format_name = $format;
            $format = $this->getNamedFormat($format_name);
        }
        else
        {
            $format_name = $this->getFormatName($format);
        }

        if(empty($format) || $format_name == 'full')
        {
            return $path;
        }

        $format = $this->regularizeFormat($format);

        $output = $this->getOutputPath($path, $format_name);

        if( ! file_exists($output) || filemtime($path) > filemtime($output) || $force)
        {
            $this->resize($path, $format, $output);
        }

        return $output;
    }

    public function regularizeFormat($format)
    {
        if( ! isset($format['width']) && ! isset($format['height']))
        {
            throw new Exceptions\FormatDimentionMissing;
        }

        if(in_array('crop', $format) || in_array('cropped', $format))
        {
            $format['crop'] = true;
        }

        // Since we extract the return of this function we must make sure
        // that keys present in the array won't cause a collision with a function
        // latter on. To prevent this we make sure that the keys present in
        // the returned array are limited to the same that are present in the default
        // array, hence the array_intersect_key.
        return array_intersect_key(
            array_merge(
                $this->defaultFormatValues,
                $format
            ),
            $this->defaultFormatValues
        );
    }

    /**
     * Return the output path
     *
     * @param  string $file
     * @param  string  $format_name
     * @return string
     */
    public function getOutputPath($file, $format_name)
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

        return "{$folder}/{$prefix}{$infos['filename']}-{$format_name}.{$infos['extension']}";
    }

    /**
     * Return the default format name
     *
     * @return string
     */
    public function getDefaultFormatName()
    {
        if(empty($this->defaultFormat))
        {
            if(count($this->formats))
            {
                return array_keys($this->formats)[0];
            }

            return false;
        }

        return $this->defaultFormat;
    }

    /**
     * Return a registered format from its name
     *
     * @param  string $format
     * @return array
     */
    public function getNamedFormat($format)
    {
        if($format == 'default')
        {
            $format = $this->getDefaultFormatName();
        }

        if(isset($this->formats[$format]))
        {
            return $this->formats[$format];
        }

        throw new Exceptions\UnknownFormatException("The format $format does not exists.");
    }

    public function getFormatSize($format)
    {
        return array_intersect_key($this->getNamedFormat($format), ['width'=>'', 'height'=>'']);
    }

    /**
     * Create the name for a format when provided one as an array
     *
     * @param  array $format
     * @return string
     */
    public function getFormatName(array $format = [])
    {
        return sprintf($this->formatNameModel,
            (isset($format['width']) ? $format['width'] : '-'),
            (isset($format['height']) ? $format['height'] : '-'),
            (isset($format['crop']) && $format['crop'] ? '-cropped' : '')
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
