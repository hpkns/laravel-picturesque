<?php

use \Hpkns\Picturesque\Picture;
use \Hpkns\Picturesque\PictureBuilder;
use \Mockery as m;

class PictureBuilderTest extends \PHPUNIT_Framework_TestCase {

    protected function tearDown()
    {
        m::close();
    }

    public function __construct()
    {
        $this->r = m::mock('\Hpkns\Picturesque\Resizer');
        $this->b = m::mock('\Illuminate\Html\HtmlBuilder');
    }

    public function testCreatesPictures()
    {
        $b = new PictureBuilder($this->r, $this->b);

        $this->assertInstanceOf('\Hpkns\Picturesque\Picture', $b->create("", ""));
    }
}

