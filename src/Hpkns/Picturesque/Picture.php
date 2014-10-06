<?php namespace Hpkns\Picturesque;

class Picture {

    /**
     * The link to the original file
     *
     * @var string
     */
    protected $path;

    /*
     * The alt attribute
     *
     * @var string
     */
    protected $alt;

    public function __construct($path = null, $alt = null)
    {
        $this->path = $path;
        $this->alt = $alt;
        $this->resizer = \App::make('picturesque.resizer');
    }

    public function getTag($format = 'full', $attributes = [])
    {
        if(empty($attributes))
        {
            $attributes['alt'] = $this->alt;
        }

        $path = $this->resizer->getPath($this->path, $format);
        $attributes = (new \Illuminate\Html\HtmlBuilder)->attributes($attributes);

        return "<img src='{$path}'{$attributes}>";
    }

    public function __get($key)
    {
        if($this->resizer->formatExists($key))
        {
            return $this->getTag($key);
        }
        throw new \Exception("Format $key does not exist. Please edit your config file to add it or use another format");
    }
}
