<?php

use \Hpkns\Picturesque\PictureResizer;
use \Mockery as m;

class PictureResizerTests extends \PHPUNIT_Framework_TestCase {

    protected $sample = [
        'path'       => 'path/to/file.jpg',
        'resized_path' => 'new/path/to/file.jpg',
        'format'     => 'thumbnail',
        'thumbnail'  => ['width' => 100],
        'formats'    => [['width' => 100]],
        'alt'        => 'Alternative text',
        'attributes' => ['class'=>'some-class'],
        'secure'     => true,
        'tag'        => '<img src="https://domain.dns/path/to/resized.jpg" alt="Alternative text" class="some-class">',
    ];

    public function tearDown()
    {
        m::close();
    }

    public function __construct()
    {
        //$this->b = m::mock('Hpkns\Picturesque\PictureBuilder');
    }

    public function testIsInstanciable()
    {
        $this->assertInstanceOf('Hpkns\Picturesque\PictureResizer', new PictureResizer(null));
    }

    public function testReturnSamePathWhenFormatEmpty()
    {
        extract($this->sample);

        $r = new PictureResizer();

        $this->assertEquals($r->getResized($path), $path);
    }

    /**
     * @expectedException Hpkns\Picturesque\Exceptions\FormatDimentionMissing
     */
    public function testRegularizeSize()
    {
        $r = new PictureResizer();

        $this->assertEquals(
            $r->regularizeSize(['width'=>100]),
            ['width'=>100, 'height'=>null, 'crop'=> false, 'filters'=>[]]
        );

        $this->assertEquals(
            $r->regularizeSize(['width'=>100, 'crop']),
            ['width' => 100, 'height' => null, 'crop' => true, 'filters' => []]
        );

        // To throw an exception. No code after this line will be executed
        $r->regularizeSize([]);
    }


    public function testResize()
    {
        extract($this->sample);

        $m = m::mock('Intervention\Image\ImageManager');
        $i = m::mock('Intervention\Image\Image');

        $r = new PictureResizer($m);

        $m->shouldReceive('make')
            ->with($path)
            ->andReturn($i);

        $i
            ->shouldReceive('fit')
            ->with(100, 200)
            ->shouldReceive('save')
            ->with($resized_path);

        $r->resize($path, ['width'=>100, 'height'=>200, 'crop'=>true], $resized_path);

        $i
            ->shouldReceive('resize')
            ->with(100, 200, m::on(function(){}))
            ->shouldReceive('save')
            ->with($resized_path);

        $r->resize($path, ['width'=>100, 'height'=>200, 'crop'=>true], $resized_path);
    }

    public function testGetNamedSize()
    {
        extract($this->sample);

        $r = new PictureResizer(null, $formats);

        //var_dump($r->getNamedSize($format));
    }
}

