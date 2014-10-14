<?php

namespace Hpkns\Picturesque;

function public_path()
{
    return '/laravel/public';
}

function realpath($path)
{
    // Value used to force if to throw an exception
    if($path != '/laravel/public/should/fail')
    {
        return $path;
    }
}

function pathinfo($file)
{
    return [
        'dirname'   => '/laravel/public/images',
        'filename'  => 'picture',
        'extension' => 'jpg',
    ];

}

function file_exists($path)
{
    if($path == '/laravel/public/cached/file.jpg')
    {
        return true;
    }
}

function filemtime($path)
{
    if($path == '/laravel/public/cached/file.jpg')
        return 2;

    return 1;
}
