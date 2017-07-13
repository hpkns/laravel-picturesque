<?php

namespace Hpkns\Picturesque\Http;

use Illuminate\Routing\Controller as BaseController;
use Hpkns\Picturesque\Database\Resizeable;

class ResizeableController extends BaseController
{
    public function resize(Resizeable $resizeable)
    {
        app('picturesque.resizer')->resize($resizeable->path, $resizeable->resized_path, $resizeable->format);
        $resizeable->delete();
        return response()->file($resizeable->resized_path);
    }
}
