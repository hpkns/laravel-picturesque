<?php

namespace Hpkns\Picturesque\Listeners;

use Hpkns\Picturesque\Events\ResizedPathCreated as Event;
use Hpkns\Picturesque\ResizedPicture as Picture;

class ResizePicture
{
    public function handle(Event $e)
    {
        if (file_exists($e->resizedPath)) {
            unlink($e->resizedPath);
        }

        if (config('picturesque.timing', 'async') == 'sync') {
            app('picturesque.resizer')->resize($e->path, $e->resizedPath, $e->format);
        } else {
            if (Picture::where('resized_path', $e->resizedPath)->count() == 0) {
                Picture::create([
                    'resized_path'  => $e->resizedPath,
                    'path'          => $e->path,
                    'format'        => $e->format->name,
                ]);
            }
        }
    }
}
