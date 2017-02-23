<?php

namespace Hpkns\Picturesque;

use Intervention\Image\ImageManager;

class PictureResizer
{
    /**
     * The quality to user for saving pictures.
     *
     * @param int
     */
    protected $quality = 80;

    /**
     * The manager.
     *
     * @var \Intervention\Image\ImageManager
     */
    protected $manager;

    /**
     * Initialize the instance.
     *
     * @param int $quality
     */
    public function __construct($quality = 80, ImageManager $manager)
    {
        $this->quality = $quality;
        $this->manager = $manager;
    }

    /**
     * Resize a picture (and much more).
     *
     * @param  string $from
     * @param  string $to
     * @param  array $format
     * @return void
     */
    public function resize($from, $to, $format)
    {
        $p = $this->manager->make($from);

        if ($format->crop) {
            $p->fit($format->width, $format->height);
        } else {
            $p->resize($format->width, $format->height, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        if (isset($format->options['resize_fill']) && ($format->width && $format->height)) {
            if (true || $format->options['resize_fill'] == 'checker') {
                $background = $this->manager->make(__DIR__ . '/../resources/checker.png');
                $background->crop($format->width, $format->height);
                $background->insert($p, 'center');
                $p = $background;
            } else {
                $p->resizeCanvas($format->width, $format->height, 'center', false, $format->options['resize_fill']);
            }
        }

        if (isset($format->options['overlay'])) {
            $overlay = $this->manager->make($format->options['overlay']);
            $p->insert($overlay, 'center');
        }

        $p->save($to);
    }
}
