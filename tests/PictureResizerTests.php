<?php

use \Hpkns\Picturesque\PictureResizer;
use \Mockery as m;

class PictureResizerTests extends \PHPUNIT_Framework_TestCase {

    protected $sample = [
        'path'       => 'path/to/file.jpg',
        'resized_path' => 'new/path/to/file.jpg',
        'format'     => 'thumbnail',
        'thumbnail'  => ['width' => 100],
        'formats'    => ['thumbnail'=>['width' => 100]],
        'alt'        => 'Alternative text',
        'attributes' => ['class'=>'some-class'],
        'secure'     => true,
        'tag'        => '<img src="https://domain.dns/path/to/resized.jpg" alt="Alternative text" class="some-class">',
    ];

    public function tearDown()
    {
        m::close();
    }

    public function testIsInstanciable()
    {
        $this->assertInstanceOf('Hpkns\Picturesque\PictureResizer', new PictureResizer(null));
    }

    public function testGetResized()
    {
        extract($this->sample);
        $r = m::mock('Hpkns\Picturesque\PictureResizer[getOutputPath,resize]', [null, $formats]);
        $r->shouldReceive('getOutputPath')
            ->once()
            ->andReturn('/vagrant/public/cached/file.jpg');

        $this->assertEquals($r->getResized($path, $format),'/vagrant/public/cached/file.jpg');

        $r->shouldReceive('getOutputPath')
            ->once()
            ->andReturn('/vagrant/public/uncached/file.jpg')
            ->shouldReceive('resize')
            ->once();
        $this->assertEquals($r->getResized($path, $format),'/vagrant/public/uncached/file.jpg');
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
            ->twice()
            ->with($path)
            ->andReturn($i);

        $i
            ->shouldReceive('fit')
            ->once()
            ->with(100, 200)
            ->shouldReceive('save')
            ->once()
            ->with($resized_path);

        $r->resize($path, ['width'=>100, 'height'=>200, 'crop'=>true], $resized_path);

        $constraint = function(){

        };

        $i
            ->shouldReceive('resize')
            ->once()
            ->with(100, 200, m::on(function($closure){
                return true;
            }))
            ->shouldReceive('save')
            ->once()
            ->with($resized_path);

        $r->resize($path, ['width'=>100, 'height'=>200, 'crop'=>false], $resized_path);
    }

    public function testGetNamedSize()
    {
        extract($this->sample);

        $r = new PictureResizer(null, $formats);

        $this->assertEquals($r->getNamedSize($format), $formats[$format]);
    }

    /**
     * @expectedException Hpkns\Picturesque\Exceptions\UnknownFormatException
     */
    public function testUnknownFormatThrowsException()
    {
        $r = new PictureResizer();

        $r->getNamedSize('Exception please!');
    }

    public function testGetSizeName()
    {
        $r = new PictureResizer();

        $this->assertEquals('100x-', $r->getSizeName(['width'=>100]));
        $this->assertEquals('-x100', $r->getSizeName(['height'=>100]));
        $this->assertEquals('10x50-cropped', $r->getSizeName(['width'=>10, 'height'=>50, 'crop'=>true]));

    }

    public function testGetOutputPath()
    {

        $file = '/vagrant/public/images/picture.jpg';
        $format = '100x200-cropped';
        $cache = '/vagrant/public/images/cache';

        $r = new PictureResizer();
        $this->assertEquals($r->getOutputPath($file, $format), "/vagrant/public/images/picture-{$format}.jpg");

        $r = new PictureResizer(null, [], $cache);
        $this->assertEquals($r->getOutputPath($file, $format), "{$cache}/".md5($file)."-picture-{$format}.jpg");


    }
}

