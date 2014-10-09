<?php

use \Hpkns\Picturesque\PictureBuilder;
use \Mockery as m;

require_once(__DIR__ . '/fakes/functions.php');

class PictureUnitTests extends \PHPUNIT_Framework_TestCase {



    protected function tearDown()
    {
        m::close();
    }

    protected function getSampleValues()
    {
        $public_path = Hpkns\Picturesque\public_path();
        $path = "path/to/file.jpg";
        $resized = "new/path/to/file.jpg";
        $domain_name = "http://website.dns";

        return [
            'public_path'       => $public_path,

            'original_path'     => $path,
            'original_abspath'  => "{$public_path}/{$path}",
            'original_asset'    => "{$domain_name}/{$path}",

            'resized_path'      => $resized,
            'resized_abspath'   => "{$public_path}/{$resized}",
            'resized_asset'     => "{$domain_name}/{$resized}",

            'format'            => 'thumbnail',
            'alt'               => 'alternate text',
            'attributes'        => ['some'=>'attribute'],
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
        $this->assertInstanceOf('\Hpkns\Picturesque\PictureBuilder', new PictureBuilder);
    }

    public function testMake()
    {
        extract($this->getSampleValues());
        $attributes['alt'] = $alt;

        $this->r->shouldReceive('getResized')
            ->with($original_abspath, $format)
            ->andReturn($resized_path);

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
    public function testGetTag()
    {
        extract($this->samples);
        $p = new Picture($path, $alt, $this->r, $this->b);

        $this->r->shouldReceive('getPath')
            ->with($path, $format)
            ->andReturn($new_path);

        $this->b->shouldReceive('attributes')
            ->with(['alt'=>$alt, 'width'=>50, 'height'=>50])
            ->andReturn($text_attrs);

        $this->assertEquals($p->getTag($format), "<img src='{$new_path}'{$text_attrs}>");
    }

    public function testDynamicAttributes()
    {
        extract($this->samples);
        $p = new Picture($path, $alt, $this->r, $this->b);

        $this->r->shouldReceive('formatExists')
            ->with($format)
            ->andReturn(true)
            ->shouldReceive('getPath')
            ->with($path, $format)
            ->andReturn($new_path);

        $this->b->shouldReceive('attributes')
            ->with(['alt'=>$alt, 'width'=>50, 'height'=>50])
            ->andReturn($text_attrs);

        $this->assertEquals($p->getTag($format), "<img src='{$new_path}'{$text_attrs}>");
    }

    /* *
     * @ expectedException \Exception
     * /
    public function testThrowExceptionIfFormatDoesNotExist()
    {
        extract($this->samples);
        $p = new Picture($path, $alt, $this->r, $this->b);
        $wrong_format = 'Some format that does not exist';

        $this->r->shouldReceive('formatExists')
            ->with($wrong_format)
            ->andReturn(false);

        $p->getTag();
    }
     */
}

