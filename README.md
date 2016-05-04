# All your base62 are belong to us

[![Latest Version](https://img.shields.io/github/release/tuupola/base62.svg?style=flat-square)](https://github.com/tuupola/base62/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/base62/master.svg?style=flat-square)](https://travis-ci.org/tuupola/base62)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/slim-jwt-auth.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/base62)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/slim-jwt-auth.svg?style=flat-square)](https://codecov.io/github/tuupola/base62)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base62
```

## Usage

``` php
$encoded = Base62::encode(random_bytes(128));
$decoded = Base62::decode($encoded);
```

## Why yet another Base62 encoder?

Because all encoders I found were encoders for integer numbers. I needed to be able to encode abritrary data. This is usefull for example when generating [random tokens for database identifiers](https://paragonie.com/blog/2015/09/comprehensive-guide-url-parameter-encryption-in-php).

``` php
$uid = Base62::encode(random_bytes(9));
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
