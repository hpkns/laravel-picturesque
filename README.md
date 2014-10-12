Picture management for Laravel
===================
[![Latest Stable Version](https://poser.pugx.org/hpkns/picturesque/v/stable.svg)](https://packagist.org/packages/hpkns/picturesque)
[![License](https://poser.pugx.org/hpkns/picturesque/license.svg)](https://packagist.org/packages/hpkns/picturesque)
[![Build Status](https://travis-ci.org/hpkns/laravel-picturesque.svg?branch=master)](https://travis-ci.org/hpkns/laravel-picturesque)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hpkns/laravel-picturesque/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hpkns/laravel-picturesque/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/hpkns/laravel-picturesque/badges/build.png?b=master)](https://scrutinizer-ci.com/g/hpkns/laravel-picturesque/build-status/master)

Picturesque allows you to simply resize and create a link to the resized version of an image in one step.

```
use Hpkns\Picturesque\Picture;

$my_picture = new Picture('/path/to/my/picture.jpg', 'My beautiful image');

echo $my_picture->thumbnail;

```

- [Usage](#usage)
  - [The PictureBuilder](#the-picturebuilder)
  - [Named formats](#named-formats)
  - [Using the Picture object](#using-the-picture-object)
- [Instalation](#instalation) 

## Usage

### The PictureBuilder
Picutre builder works like Laravel's `Html::image()`, but with the added bonus that you can specify a size for the the displayed image. `PictureBuilder` will take care of resizing the image and caching the resized version for you. By default, the resized version is saved in the same directory as the original image.

```php
$format = [
  'width'  => 200,
  'height' => 400,
  'crop'
];

echo Picture::getResized('/laravel/public/images/my-image.jpg', $format);

// Prints <img src="/laravel/public/images/my-image-200x400-cropped.jpg>
```

If you don't want your image to be cropped, you can set the `'crop'` key to `false` or simply ommit it. If you don't indicate the height of the width, il will be resized using the same aspect ratio as your original image.

```php
echo Picture::getResized('/laravel/public/images/my-image.jpg', ['width' => 300]);

// Prints <img src="/laravel/public/images/my-image-300x-.jpg">
// The resulting image will keep the same 
// aspect ratio as that of the origin image
```

### Named formats
Always providing a format for your resized pictures can be a little cumbersome. With Picturesque, you can create a list of named format ahead of time and then just use those names instead of supplying an array each time you call `getResized()`.

To use the functionality, your need to publish Picturesque's config. Run:

```bash
$ php artisan config:publish hpkns/picturesque
```

You'll find a `config.php` file in your `app/config/packages/hpkns/picturesque` folder. You can add as many formats as you want to the `$formats` array.

```php
$formats => [

  'thumbnail' => [
    'width'  => 200,
    'height' => 400,
    'crop',
  ],

  'poster' => [
    'width'  => 600
  ]
    
],
```

Once you've done that, you can use those formats names when you use `getResized()`.

```php
echo Picture::getResized('/laravel/public/images/my-image.jpg', 'thumbnail');
```

A word of caution: if you call `getResized()` with a non defined picture size, il will throw an exception.

### Using the Picture object
The `Picture` class makes using Picuresque even simpler. Just create an instance providing a path and an (optional) alt:

```php
use Hpkns\Picturesque\Picture;

$my_picture = new Picture('path/to/my/image.jpg', 'Content of the alt tag');
```

You can now use this picture everywhere you need it, e.g. in your templates:

```php
{{ $my_picture->getResized('thumbnail') }}
```

`getResized()` can take two more optionnal arguments: an array containing a list of attributes that will be added to `<img>`, and a `$secure` switch to force the use of https.

To make thing even simpler, you can simply use the format an attribute of the image. The previous example will become:

```php
{{ $my_picture->thumbnail }}
```

Formats can also be used as a methods of `Picture`, allowing you to pass the same optional arguments as `getResized`.

```php
// Exactly as $my_picture->thumbnail 
$my_picture->thumbnail()

// With a list of attributes
$my_picture->thumbnail(['class'=>'thumbnail'])

// Forcing the use of HTTPS
$my_picture->thumbnail(['class'=>'thumbnail'], true)

```

## Instalation 
PHP 5.4+ or HHVM 3.2+, and Composer are required.

To get the latest version of Picturesque, simply require `"hpkns/picturesque": "~1.0"` in your `composer.json` file. You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Picturesque is installed, you need to register the service provider. Open up `app/config/app.php` and add the following to the `providers` key.

```php
'Hpkns\Picturesque\PicturesqueServiceProvider'
```

If you want to use the Picture facade, you can register it in the aliases key of your app/config/app.php.

```php
'Picture' => 'Hpkns\Picturesque\Facades\Picture'
```
