<?php

use \Hpkns\Picturesque\Picture;
use \Mockery as m;

class UnitTests extends PHPUNIT_Framework_TestCase {


    protected $samples = [
        'path'      => 'path/to/picture.jpg',
        'new_path'  => 'path/to/cached.jpg',
        'format'    => 'thumbnail',
        'alt'       => 'alternate text',
        'attributes'=> ['some','attributes'],
    ];

    protected function tearDown()
    {
        m::close();
    }

    public function __construct()
    {
        $this->r = m::mock('\Hpkns\Picturesque\Resizer');
        $this->b = m::mock('\Illuminate\Html\HtmlBuilder');
    }

    public function testIsInstanciable()
    {
        $this->assertInstanceOf('\Hpkns\Picturesque\Picture', new Picture(null, null, $this->r, $this->b));
    }

    public function testGetTag()
    {
        extract($this->samples);
        $p = new Picture($path, $alt, $this->r, $this->b);

        $this->r->shouldReceive('getPath')
            ->with($path, $format)
            ->andReturn($new_path);
        $this->b->shouldReceive('attributes')
            ->with(['alt'=>$alt])
            ->andReturn(' focus');

        $this->assertEquals($p->getTag($format), "<img src='{$new_path}' focus>");
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
            ->with(['alt'=>$alt])
            ->andReturn(' focus');

        $this->assertEquals($p->getTag($format), "<img src='{$new_path}' focus>");
    }

    /**
     * @expectedException \Exception
     */
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
}

