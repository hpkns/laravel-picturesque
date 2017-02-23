<?php

namespace Hpkns\Picturesque;

use Illuminate\Database\Eloquent\Model;

class ResizedPicture extends Model
{
    /**
     * @var string
     */
    public $table = 'picturesque_pictures';

    /**
     * @var array
     */
    protected $fillable = ['path', 'resized_path', 'format'];
}
