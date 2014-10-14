<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File sizes
    |--------------------------------------------------------------------------
    |
    | A list of named formats to be used by a picture resizer. The width and
    | height keys are mendatory, even though the can be assigned a velue of
    | null. Crop can be ommited. It is the same as setting it to false (no crop).
    |
    | Exemple:
    |
    | 'small' => [
    |   'width'  => 200,
    |   'height' => 300,
    |   'crop'   => true
    | ]
    |
    */
    'formats' => [


    ],

    /*
    |--------------------------------------------------------------------------
    | Default format
    |--------------------------------------------------------------------------
    |
    | The name of the format used whe __toString() is called on a Picture.
    | If left blank, the fist found format will be used
    |
     */
    'default-format' => '',

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | The name of the folder where picture resizer will store your resized
    | images. The folder must exists, otherwise your application will fail
    | because Intervention\Image will try to write to a non existent folder.
    |
    | The cache path must be relative to Laravel's public directory.
    |
    | If ommited or set to null, it will cause PictureResizer to store resized
    | images is the same folder as their source.
    |
    | Exemple:
    |
    | 'cache' => '/images/cache',
    */
    'cache' => null,
];
