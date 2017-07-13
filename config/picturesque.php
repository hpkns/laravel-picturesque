<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Formats
    |--------------------------------------------------------------------------
    |
    | A list of named formats to be used by a picture resizer. The width and the
    | height cannot be ommited in a format and non cropped formats require to have
    | both defined. Crop can be used as a flag ('crop'), or can be assigned a
    | boolean value ('crop'=>true).
    |
    | Exemple:
    |
    | 'small' => [200, 200, false, ['resize_fill' => '#eee']]
    |
    */
    'formats' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Resize timing
    |--------------------------------------------------------------------------
    |
    | This option determins when the picture is resized:
    |
    | - 'sync': resizes the picture when the tag is required
    | - 'async': resizes the picture at display
    |
     */
    'timing' => 'sync',

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | The name of the folder where picture resizer will store your resized
    | images. The folder must exists, otherwise your application will fail
    | because Intervention\Image will try to write to a non existent folder.
    |
    | If ommited or set to null, it will cause PictureResizer to store resized
    | images is the same folder as their source.
    |
    | Exemple:
    |
    | 'cache' => public_path('images/cache'),
     */
    'cache' => public_path('images/cache'),

    'filters' => [
        'darken'  => Hpkns\Picturesque\Image\Filters\Darken::class,
        'fill'    => Hpkns\Picturesque\Image\Filters\Fill::class,
        'overlay' => Hpkns\Picturesque\Image\Filters\Overlay::class,
    ],
];
