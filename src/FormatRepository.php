<?php

namespace Hpkns\Picturesque;

class FormatRepository
{
    /**
     *  A list of formats known to the instance
     *
     * @var array
     */
    protected $formats;

    /**
     * The name of the default format format
     *
     * @var string
     */
    protected $defaultFormat;

    /**
     * Format sprintf model
     *
     * @var string
     */
    protected $formatNameModel = "%sx%s%s";

    /**
     * Default format hash
     *
     * @var string
     */
    protected $defaultFormatValues = [
        'width'   => null,
        'height'  => null,
        'crop'    => false,
        'postfix' => null,
    ];

    /**
     * An formats in bulk.
     *
     * @param  array $formats
     * @return $this
     */
    public function addFormats($formats)
    {
        foreach ($formats as $name => $format) {
            $this->addFormat($name, $format);
        }

        return $this;
    }

    /**
     * Add a named format.
     *
     * @param  string $name
     * @param  array  $details
     * @return void
     */
    public function addFormat($name, $details)
    {
        $this->formats[$name] = $this->regularizeFormat($details, $name);
    }


    /**
     * Return a format.
     *
     * @param  mixed $name
     * @return mixed
     */
    public function get($name = null)
    {
        if (is_array($name)) {
            return $this->regularizeFormat($name);
        }

        if (empty($name)) {
            if (!empty($this->defaultFormat)) {
                $name  = $this->defaultFormat;
            }
        }

        if (array_key_exists($name, $this->formats)) {
            return $this->formats[$name];
        }

        throw Exceptions\UnknownFormatException;
    }

    /**
     * Regularize format settings.
     *
     */
    public function regularizeFormat($format, $name = null)
    {
        if (empty($format)) {
            throw new Exceptions\FormatDimentionMissing;
        }

        if (is_int(array_keys($format)[0])) {
            $format['width']  = $format[0];
            $format['height'] = isset($format[1]) ? $format[1] : null;
            $format['crop']   = isset($format[2]) ? $format[2] : false;
        }

        if( ! isset($format['width']) && ! isset($format['height'])) {
        }

        if(in_array('crop', $format) || in_array('cropped', $format)) {
            $format['crop'] = true;
        }

        $format['postfix'] = empty($name) ? $this->getDefaultFormatName($format) : $name;

        // Since we extract the return of this function we must make sure
        // that keys present in the array won't cause a collision with a function
        // latter on. To prevent this we make sure that the keys present in
        // the returned array are limited to the same that are present in the default
        // array, hence the array_intersect_key.
        return array_intersect_key(
            array_merge(
                $this->defaultFormatValues,
                $format
            ),
            $this->defaultFormatValues
        );
    }

    /**
     * Create the name for a format when provided one as an array
     *
     * @param  array $format
     * @return string
     */
    public function getDefaultFormatName(array $format = [])
    {
        return sprintf($this->formatNameModel,
            (isset($format['width']) ? $format['width'] : '-'),
            (isset($format['height']) ? $format['height'] : '-'),
            (isset($format['crop']) && $format['crop'] ? '-cropped' : '')
        );
    }
}

