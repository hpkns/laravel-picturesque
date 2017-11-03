<?php

namespace Hpkns\Picturesque\Http;

use Hpkns\Picturesque\Cache;
use Hpkns\Picturesque\FormatRepository;
use Hpkns\Picturesque\Resizer;
use Hpkns\Picturesque\Support\Contracts\ResizePromise;
use Illuminate\Routing\Controller as BaseController;

class PromiseController extends BaseController
{
    /**
     * @var \Hpkns\Picturesque\FormatRepository
     */
    protected $formats;

    /**
     * @var \Hpkns\Picturesque\Cache
     */
    protected $cache;

    /**
     * @var \Hpkns\Picturesque\Resizer
     */
    protected $resizer;

    /**
     * Initialize the image.
     *
     * @param string $path
     */
    public function __construct(FormatRepository $formats = null, Cache $cache = null, Resizer $resizer)
    {
        $this->formats = $formats;
        $this->cache = $cache;
        $this->resizer = $resizer;
    }

    public function fullfill(ResizePromise $promise)
    {
        $format = $this->formats->get($promise->format);
        $cached_name = $this->cache->getName($promise->path, $format);
        $image = $this->resizer->resize($promise->path, $format);
        $url = $this->cache->save($image, $cached_name);

        return redirect()->to($url);
    }
}
