# Base62

This library implements base62 encoding. In addition to integers it can encode and decode any arbitrary data. This is useful for example when generating url safe [random tokens for database identifiers](https://paragonie.com/blog/2015/09/comprehensive-guide-url-parameter-encryption-in-php).


[![Latest Version](https://img.shields.io/packagist/v/tuupola/base62.svg?style=flat-square)](https://packagist.org/packages/tuupola/base62)
[![Packagist](https://img.shields.io/packagist/dm/tuupola/base62.svg)](https://packagist.org/packages/tuupola/base62)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/workflow/status/tuupola/base62/Tests/2.x?style=flat-square)](https://github.com/tuupola/base62/actions)
[![Coverage](https://img.shields.io/codecov/c/github/tuupola/base62.svg?style=flat-square)](https://codecov.io/github/tuupola/base62)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base62
```

This branch requires PHP 7.1 or up. The older `1.x` branch supports also PHP 5.6 and 7.0.

``` bash
$ composer require "tuupola/base62:^1.0"
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
$base62 = new Tuupola\Base62;

$encoded = $base62->encode(random_bytes(128));
$decoded = $base62->decode($encoded);
```

If you are encoding to and from integer use the implicit `decodeInteger()` and `encodeInteger()` methods.

``` php
$integer = $base62->encodeInteger(987654321); /* 14q60P */
print $base62->decodeInteger("14q60P"); /* 987654321 */
```

Note that encoding a string and an integer will yield different results.

``` php
$string = $base62->encode("987654321"); /* KHc6iHtXW3iD */
$integer = $base62->encodeInteger(987654321); /* 14q60P */
```

## Character sets

By default Base62 uses GMP style character set. Shortcut is provided for the inverted character set which is also commonly used. You can also use any custom character set of 62 unique characters.

```php
use Tuupola\Base62;

print Base62::GMP; /* 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz */
print Base62::INVERTED; /* 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ */

$default = new Base62(["characters" => Base62::GMP]);
$inverted = new Base62(["characters" => Base62::INVERTED]);
print $default->encode("Hello world!"); /* T8dgcjRGuYUueWht */
print $inverted->encode("Hello world!"); /* t8DGCJrgUyuUEwHT */
```

## Speed

Install GMP if you can. It is much faster pure PHP encoder. Below benchmarks are for encoding `random_bytes(128)` data. BCMatch encoder is also included but it is mostly just a curiosity. It is too slow to be usable.

```
$ vendor/bin/phpbench run benchmarks/ --report=default

+-----------------------+------------------+-----------+
| subject               | mean             | diff      |
+-----------------------+------------------+-----------+
| benchGmpEncoder       | 110,619.469ops/s | 1.00x     |
| benchGmpEncoderCustom | 106,157.113ops/s | 1.04x     |
| benchPhpEncoder       | 373.204ops/s     | 296.40x   |
| benchBcmathEncoder    | 35.494ops/s      | 3,116.58x |
+-----------------------+------------------+-----------+
```

## Static Proxy

If you prefer to use static syntax use the provided static proxy.

``` php
use Tuupola\Base62Proxy as Base62;

$encoded = Base62::encode(random_bytes(128));
$decoded = Base62::decode($encoded);

$encoded2 = Base62::encodeInteger(987654321);
$decoded2 = Base62::decodeInteger($encoded2);
```

## Testing

You can run tests either manually or automatically on every code change. Automatic tests require [entr](http://entrproject.org/) to work.

``` bash
$ make test
```
``` bash
$ brew install entr
$ make watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
