<?php

namespace Hpkns\Picturesque;

use Intervention\Image\ImageManager;

class PictureResizer implements Contracts\PictureResizerContract
{
    /**
     * A manager to resize images with.
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
    public function __construct(ImageManager $manager = null)
    {
        $this->manager = $manager ?: new ImageManager;
    }

    /**
     * Resizes an image to the desired format
     *
     * @param  string $input
     * @param  array  $format
     * @param  string $output
     * @return string
     */
    public function resize($input, $output, array $format, $force = false)
    {
        if (file_exists($output) && !$force && filemtime($output) > filemtime($input)) {
            return $output;
        }

        extract($format);

        $img = $this->manager->make($input);

        if($crop) {
            $img->fit($width, $height);
        } else {
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });;
        }

        $img->save($output);
    }
}
