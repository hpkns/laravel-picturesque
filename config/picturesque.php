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
        'small'     => [300, 200, true],
        'medium'    => [400, 300, true],
        'medium_2x' => [800, 600, true],
        'large'     => [800, 600, true],
    ],

    'default-format' => null,

    /*
    |--------------------------------------------------------------------------
    | Picture formats
    |--------------------------------------------------------------------------
    |
    | A list of <picture> formats.
    */
    'picture_formats' => [
        'thumbnail' => [
            'sources' => [
                ['srcset' => ':medium, :medium_2x 2x', 'media' => '(max-width: 500px)'],
                ['srcset' => ':large','media' => '(min-width: 500px)']
            ],
            'default' => 'large',
        ],
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
    | File location
    |--------------------------------------------------------------------------
     */
    'path_base' => null,

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
];
