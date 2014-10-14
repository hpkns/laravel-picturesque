<?php namespace Hpkns\Picturesque\Contracts;

interface PictureResizerContract {

    /**
     * Resize an image and returns the path to the newly created one
     *
     * @param  string $path
     * @param  mixed  $formatatat
     * @return string
     */
    public function getResized($path, $format);
}
