# Base62

This library implements base62 encoding. In addition to integers it can encode and decode any arbitrary data. This is useful for example when generating url safe [random tokens for database identifiers](https://paragonie.com/blog/2015/09/comprehensive-guide-url-parameter-encryption-in-php). For quick testing try the [online base62 encoder](http://base62.net).


[![Latest Version](https://img.shields.io/packagist/v/tuupola/base62.svg?style=flat-square)](https://packagist.org/packages/tuupola/base62)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/base62/master.svg?style=flat-square)](https://travis-ci.org/tuupola/base62)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/base62.svg?style=flat-square)](https://codecov.io/github/tuupola/base62)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base62
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
$base62 = new Tuupola\Base62;

$encoded = $base62->encode(random_bytes(128));
$decoded = $base62->decode($encoded);
```

Note that if you are encoding to and from integer you need to pass boolean `true` as the second argument for `decode()` method. This is because `decode()` does not know if the original data was an integer or binary data.

``` php
$integer = $base62->encode(987654321); /* 14q60P */
print $base62->decode("14q60P", true); /* 987654321 */
```

If you prefer you can also use the implicit `decodeInteger()` method.

``` php
$integer = $base62->encode(987654321); /* 14q60P */
print $base62->decodeInteger("14q60P"); /* 987654321 */
```

Also note that encoding a string and an integer will yield different results.

``` php
$integer = $base62->encode(987654321); /* 14q60P */
$string = $base62->encode("987654321"); /* KHc6iHtXW3iD */
```

## Character sets

By default Base62 uses GMP style character set. Shortcut is provided for the inverted character set which is also commonly used. You can also use any custom character set of 62 unique characters.

```php
use Tuupola\Base62;

print Base62:GMP; /* 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz */
print Base62:INVERTED; /* 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ */

$default = new Base62(["characters" => Base62:GMP]);
$inverted = new Base62(["characters" => Base62:INVERTED]);
print $default->encode("Hello world!"); /* T8dgcjRGuYUueWht */
print $inverted->encode("Hello world!"); /* t8DGCJrgUyuUEwHT */
```

## Speed

Install GMP if you can. It is much faster pure PHP encoder. Below benchmarks are for encoding `random_bytes(128)` data. BCMatch encoder is also included but it is mostly just a curiosity. It is too slow to be usable.

```
$ phpbench run benchmarks/ --report=default

+-----------------------+-----------------+----------------+
| subject               | mean            | diff           |
+-----------------------+-----------------+----------------+
| benchGmpEncoder       | 73,099.415ops/s | 0.00%          |
| benchGmpEncoderCustom | 61,349.693ops/s | +19.15%        |
| benchPhpEncoder       | 25.192ops/s     | +290,072.37%   |
| benchBcmathEncoder    | 7.264ops/s      | +1,006,253.07% |
+-----------------------+-----------------+----------------+
```

## Static Proxy

If you prefer to use static syntax use the provided static proxy.

``` php
use Tuupola\Base62Proxy as Base62;

$encoded = $base62::encode(random_bytes(128));
$decoded = $base62::decode($encoded);
```

## Short UUID

If you are already using UUIDs they can be encoded.

``` php
use Ramsey\Uuid\Uuid;
use Tuupola\Base62Proxy as Base62;

$uuid = Uuid::fromString("d84560c8-134f-11e6-a1e2-34363bd26dae");
Base62::encode($uuid->getBytes()); /* 6a630O1jrtMjCrQDyG3D3O */
$uuid = Uuid::fromBytes(Base62::decode("6a630O1jrtMjCrQDyG3D3O"));
print $uuid; /* d84560c8-134f-11e6-a1e2-34363bd26dae */
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
