# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [0.10.0](https://github.com/tuupola/base62/compare/0.9.0...master) - unreleased

### Changed
- The `decode()` and `decodeInteger()` methods now throw `InvalidArgumentException` if the input string contains invalid characters ([#6](https://github.com/tuupola/base62/pull/6)).
- Constructor now throws `InvalidArgumentException` if given character set is invalid ([#7](https://github.com/tuupola/base62/pull/7)).

## [0.9.0](https://github.com/tuupola/base62/compare/0.8.0...0.9.0) - 2017-10-09

### Added
- Implicit `decodeInteger()` and `encodeInteger()` methods.

### Fixed
- PHP encoder was returning returning wrong output when encoding integers and the float representation of the integer was wider than 53 bits. If your application uses big integers and PHP encoder only might be BC issues with `0.8.0`. GMP and BCMath encoders were not affected.

## [0.8.0](https://github.com/tuupola/base62/compare/0.7.0...0.8.0) - 2017-03-12

This release is not compatible with `0.7.0`. Object syntax is now default. A quick way to upgrade is to add the following to your code:

```php
use Tuupola\Base62Proxy as Base62;
```

### Added
- Possibility to use custom character sets.
- Static proxy for those who want to use static syntax
    ```php
    use Tuupola\Base62Proxy as Base62;

    Base62::decode("foo");
    ```

## [0.7.0](https://github.com/tuupola/base62/compare/0.6.0...0.7.0) - 2016-10-09
### Added

- Allow using object syntax, for example `$base62->decode("foo")`.
- Optional BCMath encoder. Mostly a curiosity since it is slower than pure PHP encoder.

## [0.6.0](https://github.com/tuupola/base62/compare/0.5.0...0.6.0) - 2016-05-06
### Fixed

- Encode integers as integers. Before they were cast as strings before encoding.

## 0.5.0 - 2016-05-05

Initial realese.
