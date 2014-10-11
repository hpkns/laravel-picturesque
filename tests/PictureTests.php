<?php

use \Hpkns\Picturesque\Picture;
use \Mockery as m;

class PictureTests extends \PHPUNIT_Framework_TestCase {

    protected $sample = [
        'path'       => 'path/to/file.jpg',
        'format'     => 'thumbnail',
        'alt'        => 'Alternative text',
        'attributes' => ['class'=>'some-class'],
        'secure'     => true,
        'tag'        => '<img src="https://domain.dns/path/to/resized.jpg" alt="Alternative text" class="some-class">',
    ];
    protected function tearDown()
    {
        m::close();
    }

    public function __construct()
    {
        $this->b = m::mock('Hpkns\Picturesque\PictureBuilder');
    }

    public function testIsInstanciable()
    {
        $this->assertInstanceOf('Hpkns\Picturesque\Picture', new Picture('path', 'alternate text', $this->b));
    }

    public function testGetTag()
    {
        extract($this->sample);

        $p = new Picture($path, $alt, $this->b);

        $this->b
            // 1: Every arguments are explicit
            ->shouldReceive('make')
            ->with($path, $format, $alt, $attributes, $secure)
            ->andReturn($tag)
            // 2: $secure is implicit (false)
            ->shouldReceive('make')
            ->with($path, $format, $alt, $attributes, false)
            ->andReturn($tag)
            // 3: $attributes ([]) and $secure a implicit
            ->shouldReceive('make')
            ->with($path, $format, $alt, [], false)
            ->andReturn($tag)
            // 4: $format (full), $attributes and $secure are implicit
            ->shouldReceive('make')
            ->with($path, 'full', $alt, [], false)
            ->andReturn($tag);

        // 1:
        $this->assertEquals($tag, $p->getTag($format, $attributes, $secure));
        // 2:
        $this->assertEquals($tag, $p->getTag($format, $attributes));
        // 3:
        $this->assertEquals($tag, $p->getTag($format));
        // 4:
        $this->assertEquals($tag, $p->getTag());
    }

    public function testDynamicProperties()
    {
        extract($this->sample);
        $p = new Picture($path, $alt, $this->b);

        $this->b
            ->shouldReceive('make')
            ->with($path, $format, $alt, [], false)
            ->andReturn($tag);

        $this->assertEquals($tag, $p->__get($format));
    }

    public function testDynamicMethods()
    {
        extract($this->sample);
        $p = new Picture($path, $alt, $this->b);

        $this->b
            // 1: Everything is implicit
            ->shouldReceive('make')
            ->with($path, $format, $alt, [], false)
            ->andReturn($tag)
            // 2: $attributes is explicit
            ->shouldReceive('make')
            ->with($path, $format, $alt, $attributes, false)
            ->andReturn($tag)
            // 3: $attributes and $secure are explicit
            ->shouldReceive('make')
            ->with($path, $format, $alt, $attributes, $secure)
            ->andReturn($tag);

        // 1:
        $this->assertEquals($tag, $p->__call($format,[]));
        // 2:
        $this->assertEquals($tag, $p->__call($format,[$attributes]));
        // 3:
        $this->assertEquals($tag, $p->__call($format,[$attributes, $secure]));
    }
}

