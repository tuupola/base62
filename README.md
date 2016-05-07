# All your Base62

[![Latest Version](https://img.shields.io/packagist/v/tuupola/base62.svg?style=flat-square)](https://packagist.org/packages/tuupola/base62)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/base62/master.svg?style=flat-square)](https://travis-ci.org/tuupola/base62)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/base62.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/base62)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/base62.svg?style=flat-square)](https://codecov.io/github/tuupola/base62)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base62
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
use Tuupola\Base62;

$encoded = Base62::encode(random_bytes(128));
$decoded = Base62::decode($encoded);
```

Install GMP if you can. It is much faster pure PHP encoder. Below benchmarks are for encoding `random_bytes(128)` data. BCMatch encoder is also included but it is mostly just a curiocity. It is too slow to be usable.

```
$ phpbench run benchmarks/ --report=default

+--------------------+---------------+---------+
| subject            | mean          | diff    |
+--------------------+---------------+---------+
| benchGmpEncoder    | 50.900μs      | 0.00%   |
| benchPhpEncoder    | 39,044.400μs  | +99.87% |
| benchBcmathEncoder | 139,278.500μs | +99.96% |
+--------------------+---------------+---------+
```

## Why yet another Base62 encoder?

Because all encoders I found were encoders only for integer numbers. I needed to be able to encode arbitrary data. This is usefull for example when generating url safe [random tokens for database identifiers](https://paragonie.com/blog/2015/09/comprehensive-guide-url-parameter-encryption-in-php).

``` php
$token = Base62::encode(random_bytes(9));
```

If you are already using UUIDs, they can also be encoded.

``` php
use Ramsey\Uuid\Uuid;
use Tuupola\Base62;

$uuid = Uuid::fromString("d84560c8-134f-11e6-a1e2-34363bd26dae");
Base62::encode($uuid->getBytes()); /* 6a630O1jrtMjCrQDyG3D3O */
$uuid = Uuid::fromBytes(Base62::decode("6a630O1jrtMjCrQDyG3D3O"));
print $uuid; /* d84560c8-134f-11e6-a1e2-34363bd26dae */
```

Note that if you are encoding to and from integer you need to pass boolean `true` as the second argument for `decode()` method. This is because `decode()` method does not know if the original data was an integer or binary data.

``` php
$integer =  Base62::encode(987654321); /* 14q60P */
print Base62::decode("14q60P", true); /* 987654321 */
```

Also note that encoding a string and an integer will yield different results.

``` php
$integer =  Base62::encode(987654321); /* 14q60P */
$string = Base62::encode("987654321"); /* KHc6iHtXW3iD */
```

## Testing

You can run tests either manually...

``` bash
$ vendor/bin/phpunit
$ vendor/bin/phpcs --standard=PSR2 src/ -p
```

... or automatically on every code change.

``` bash
$ npm install
$ grunt watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
