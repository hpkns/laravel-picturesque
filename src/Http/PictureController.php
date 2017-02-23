<?php

namespace Hpkns\Picturesque\Http;

use Illuminate\Http\Request;
use Hpkns\Picturesque\ResizedPicture;
use Hpkns\Picturesque\Format;
use Illuminate\Routing\Controller;

class PictureController extends Controller
{
    /**
     * Show resized picture.
     *
     * @param  string $path
     * @return \Illuminate\Http\Response
     */
    function showResized($path)
    {
        $picture = ResizedPicture::where('resized_path', 'like', "%{$path}")->firstOrFail();
        $format = app('picturesque.formats')->get($picture->format);
        app('picturesque.resizer')->resize($picture->path, $picture->resized_path, $format);
        $picture->delete();
        return response()->file($picture->resized_path);
    }
}
