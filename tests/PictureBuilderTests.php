<?php

use \Hpkns\Picturesque\PictureBuilder;
use \Mockery as m;

require_once __DIR__.'/fakes/functions.php';

class PictureBuilderTests extends \PHPUNIT_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    protected function getSampleValues()
    {
        $public_path = Hpkns\Picturesque\public_path();
        $path = 'path/to/file.jpg';
        $resized = 'new/path/to/file.jpg';
        $domain_name = 'http://website.dns';

        return [
            'public_path' => $public_path,

            'original_path' => $path,
            'original_abspath' => "{$public_path}/{$path}",
            'original_asset' => "{$domain_name}/{$path}",
            'failing_path' => 'should/fail',

            'resized_path' => $resized,
            'resized_abspath' => "{$public_path}/{$resized}",
            'resized_asset' => "{$domain_name}/{$resized}",

            'format' => 'thumbnail',
            'alt' => 'alternate text',
            'attributes' => ['some' => 'attribute'],
            'attributes_parsed' => ' some="attribute" alt="alternate text"',
        ];
    }
    public function __construct()
    {
        $this->r = m::mock('Hpkns\Picturesque\Contracts\PictureResizerContract');
        $this->b = m::mock('Illuminate\Html\HtmlBuilder');
        $this->g = m::mock('Illuminate\Routing\UrlGenerator');
    }

    public function testIsInstanciable()
    {
        $this->assertInstanceOf('\Hpkns\Picturesque\PictureBuilder', new PictureBuilder());
    }

    public function testMake()
    {
        extract($this->getSampleValues());
        $attributes['alt'] = $alt;

        $this->r
            ->shouldReceive('getFormatSize')
            ->once()
            ->with($format)
            ->andReturn([])
            ->shouldReceive('getResized')
            ->once()
            ->with($original_abspath, $format)
            ->andReturn($resized_abspath);

        $this->g->shouldReceive('asset')
            ->with($resized_path, false)
            ->andReturn($resized_asset);

        $this->b->shouldReceive('attributes')
            ->with($attributes)
            ->andReturn($attributes_parsed);

        $b = new PictureBuilder($this->r, $this->b, $this->g);

        $r = $b->make($original_path, $format, $alt, $attributes, false);

        $this->assertEquals("<img src=\"{$resized_asset}\"{$attributes_parsed}>", $r);
    }

    /**
     * @expectedException Hpkns\Picturesque\Exceptions\NotInPublicPathException
     *
     * @see realpath() in fakes/functions.php
     */
    public function testFailsIfResizedPictureNotInPulicPath()
    {
        extract($this->getSampleValues());
        $attributes['alt'] = $alt;

        $this->r
            ->shouldReceive('getFormatSize')
            ->once()
            ->with($format)
            ->andReturn([])
            ->shouldReceive('getResized')
            ->andReturn($failing_path);

        $b = new PictureBuilder($this->r, $this->b, $this->g);

        $b->make($original_path, $format, $alt, $attributes, false);
    }
    /**
     * @expectedException Hpkns\Picturesque\Exceptions\WrongPathException
     *
     * @see realpath() in fakes/functions.php
     */
    public function testFailsIfPictureDoesNotExists()
    {
        extract($this->getSampleValues());

        $b = new PictureBuilder($this->r, $this->b, $this->g);

        $this->r->shouldReceive('getFormatSize')
            ->once()
            ->with('')
            ->andReturn([]);

        $b->make($failing_path);
    }
}
