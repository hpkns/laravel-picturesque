<?php

namespace Hpkns\Picturesque;

function public_path(){
    return '/laravel/public';
}

function realpath($path){
    // Value used to force if to throw an exception
    if($path != '/laravel/public/should/fail')
    {
        return $path;
    }
}
