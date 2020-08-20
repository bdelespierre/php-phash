# PHP pHash Implementation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bdelespierre/php-phash.svg?style=flat-square)](https://packagist.org/packages/bdelespierre/php-phash)
[![Build Status](https://img.shields.io/travis/bdelespierre/php-phash/master.svg?style=flat-square)](https://travis-ci.org/bdelespierre/php-phash)
[![Quality Score](https://img.shields.io/scrutinizer/g/bdelespierre/php-phash.svg?style=flat-square)](https://scrutinizer-ci.com/g/bdelespierre/php-phash)
[![Total Downloads](https://img.shields.io/packagist/dt/bdelespierre/php-phash.svg?style=flat-square)](https://packagist.org/packages/bdelespierre/php-phash)

Performs syntax-checks of your Blade templates. Just that.

## Installation

You can install the package via composer:

```bash
composer require bdelespierre/php-phash
```

## Usage

```bash
vendor/bin/phash generate <image>
vendor/bin/phash compare <image1> <image2>
```

```PHP
require "vendor/autoload.php";

use Bdelespierre\PhpPhash\PHash;
use Intervention\Image\ImageManager;

$manager = new ImageManager(['driver' => 'imagick']);
$phash = new PHash($manager);

$hash = $phash->hash(new \SplFileInfo("image.jpg"));
$bash_hex = base_convert($bits, 2, 16);

echo $base_hex; // ffffef0001900000
```

Compare 2 hashes using [Hamming Distance](https://en.wikipedia.org/wiki/Hamming_distance)

```PHP
$hash1 = $phash->hash(new \SplFileInfo("image1.jpg"));
$hash2 = $phash->hash(new \SplFileInfo("image2.jpg"));

$dist = 0;
for ($i = 0; $i < $size ** 2; $i++) {
    if ($hash1[$i] != $hash2[$i]) {
        $dist++;
    }
}

echo "Hamming distance is: {$dist}";
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email benjamin.delespierre@gmail.com instead of using the issue tracker.

## Credits

- [Benjamin Delespierre](https://github.com/bdelespierre)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

