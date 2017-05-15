<?php

namespace Hpkns\Picturesque\Database;

use Illuminate\Database\Eloquent\Model;
use Hpkns\Picturesque\Formats\Format;

class Resizeable extends Model
{
    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'resized_path', 'format'
    ];

    /**
     * Return the format.
     *
     * @return \Hpkns\Picturesque\Formats\Format
     */
    public function getFormatAttribute()
    {
        if (array_key_exists('format', $this->attributes)) {
            return Format::fromJson($this->attributes['format']);
        }
    }
}
