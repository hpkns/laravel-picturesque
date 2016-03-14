<?php

use \Hpkns\Picturesque\PictureResizer;
use \Mockery as m;

class PictureResizerTests extends \PHPUNIT_Framework_TestCase
{
    protected $sample = [
        'path' => 'path/to/file.jpg',
        'resized_path' => 'new/path/to/file.jpg',
        'format' => 'thumbnail',
        'thumbnail' => ['width' => 100],
        'formats' => ['thumbnail' => ['width' => 100], 'large' => [600, 200]],
        'alt' => 'Alternative text',
        'attributes' => ['class' => 'some-class'],
        'secure' => true,
        'tag' => '<img src="https://domain.dns/path/to/resized.jpg" alt="Alternative text" class="some-class">',
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
            ->andReturn('/laravel/public/cached/file.jpg');

        $this->assertEquals($r->getResized($path, $format), '/laravel/public/cached/file.jpg');

        $r->shouldReceive('getOutputPath')
            ->once()
            ->andReturn('/laravel/public/uncached/file.jpg')
            ->shouldReceive('resize')
            ->once();
        $this->assertEquals($r->getResized($path, $format), '/laravel/public/uncached/file.jpg');

        $r->shouldReceive('getOutputPath')
            ->once()
            ->andReturn('/laravel/public/uncached/file.jpg')
            ->shouldReceive('resize')
            ->once();
        $this->assertEquals($r->getResized($path, 'default'), '/laravel/public/uncached/file.jpg');
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
    public function testregularizeFormat()
    {
        $r = new PictureResizer();

        $this->assertEquals(
            $r->regularizeFormat(['width' => 100]),
            ['width' => 100, 'height' => null, 'crop' => false, 'filters' => []]
        );

        $this->assertEquals(
            $r->regularizeFormat(['width' => 100, 'crop']),
            ['width' => 100, 'height' => null, 'crop' => true, 'filters' => []]
        );

        // To throw an exception. No code after this line will be executed
        $r->regularizeFormat([]);
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

        $r->resize($path, ['width' => 100, 'height' => 200, 'crop' => true], $resized_path);

        $constraint = function () {

        };

        $i
            ->shouldReceive('resize')
            ->once()
            ->with(100, 200, m::on(function ($closure) {
                return true;
            }))
            ->shouldReceive('save')
            ->once()
            ->with($resized_path);

        $r->resize($path, ['width' => 100, 'height' => 200, 'crop' => false], $resized_path);
    }

    public function testGetDefaultFormatName()
    {
        extract($this->sample);

        $r = new PictureResizer(null, $formats, null, 'large');

        $this->assertEquals('large', $r->getDefaultFormatName());

        $r = new PictureResizer(null, $formats);

        $this->assertEquals($format, $r->getDefaultFormatName());

        $r = new PictureResizer(null, []);

        $this->assertEquals(false, $r->getDefaultFormatName());
    }
    public function testGetNamedFormat()
    {
        extract($this->sample);

        $r = new PictureResizer(null, $formats);

        $this->assertEquals($r->getNamedFormat($format), $formats[$format]);
    }

    /**
     * @expectedException Hpkns\Picturesque\Exceptions\UnknownFormatException
     */
    public function testUnknownFormatThrowsException()
    {
        $r = new PictureResizer();

        $r->getNamedFormat('Exception please!');
    }

    public function testgetFormatName()
    {
        $r = new PictureResizer();

        $this->assertEquals('100x-', $r->getFormatName(['width' => 100]));
        $this->assertEquals('-x100', $r->getFormatName(['height' => 100]));
        $this->assertEquals('10x50-cropped', $r->getFormatName(['width' => 10, 'height' => 50, 'crop' => true]));
    }

    public function testGetOutputPath()
    {
        $file = '/laravel/public/images/picture.jpg';
        $format = '100x200-cropped';
        $cache = '/laravel/public/images/cache';

        $r = new PictureResizer();
        $this->assertEquals($r->getOutputPath($file, $format), "/laravel/public/images/picture-{$format}.jpg");

        $r = new PictureResizer(null, [], $cache);
        $this->assertEquals($r->getOutputPath($file, $format), "{$cache}/".md5($file)."-picture-{$format}.jpg");
    }
}
