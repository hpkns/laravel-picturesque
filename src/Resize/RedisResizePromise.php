<?php

namespace Hpkns\Picturesque\Resize;

use Hpkns\Picturesque\Support\Contracts\ResizePromise;
use Redis;
use Hpkns\Picturesque\Support\Concerns\HasAttributes;

class RedisResizePromise implements ResizePromise
{
    use HasAttributes;

    public function create($attributes)
    {
        $instance = new static;
        $instance->fill($attributes);
        $instance->id = md5($instance->path) . "-{$instance->format->name}";
        $instance->save();

        return $instance;
    }

    public function save()
    {
        Redis::hset("picturesque.promises", $this->id, json_encode([
            'path' => $this->path,
            'format' => $this->format->name
        ]));
    }

    public function find($id)
    {
        $attributes = Redis::hget('picturesque.promises', $id);

        if ($attributes !== null) {
            return (new static)->fill(json_decode($attributes));
        }
    }

    public function getRoute()
    {
        return route('picturesque.promise', $this->id);
    }

    /**
    public function create($attributes)
    {
        extract($attributes);

        $id = ;

        return new class implements ResizePromise {
            public function getRoute() {
            }
        };
    }
//    protected $created = false;
//
//    /**
//     *
//     */
//    public function __construct($attributes = [])
//    {
//        if (isset($attributes['path']) && $attributes['format']) {
//            $this->save($attributes['path'], $attributes['format']);
//        }
//    }
//
//    public function save($path, $format)
//    {
//        $this->created = true;
//    }
//
//    public function create(array $attributes)
//    {
//        return new static($attributes);
//    }
//
//    public function find($id)
//    {
//    }
//
//    public function hydrate($attributes)
//    {
//        return $this;
//    }
//
//    public function getRoute()
//    {
//        if ($this->created) {
//        }
//    }
//}
}
