# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [2.1.0](https://github.com/tuupola/base62/compare/2.0.0...2.1.0) - 2020-09-09

### Added
- Allow installing with PHP 8 ([#20](https://github.com/tuupola/base62/pull/20)).

## [2.0.0](https://github.com/tuupola/base62/compare/1.0.1...2.0.0) - 2018-12-30

### Changed
- PHP 7.1 is now minimum requirement
- All methods have return types
- All methods are typehinted
- All type juggling is removed

## [1.0.1](https://github.com/tuupola/base62/compare/1.0.0...1.0.1) - 2018-12-27
### Removed
- The unused and undocumented second parameter from static proxy methods. Parameter was accidentally forgotten into the code. This could be considered a BC break. However since there have been practically no installs for 1.0.0 yet I doubt this will break anyones code.

## [1.0.0](https://github.com/tuupola/base62/compare/0.11.1...1.0.0) - 2018-12-20

### Changed
- Tests are now run with all character sets ([#14](https://github.com/tuupola/base62/pull/14)).

## [0.11.1](https://github.com/tuupola/base62/compare/0.11.0...0.11.1) - 2018-09-11

### Fixed
- GMP driver output was not matching others when binary data had leading 0x00 ([#13](https://github.com/tuupola/base62/pull/13)).

## [0.11.0](https://github.com/tuupola/base62/compare/0.10.0...0.11.0) - 2018-09-05

### Fixed
- Leading 0x00 was stripped from binary data ([#4](https://github.com/tuupola/base62/issues/4), [#12](https://github.com/tuupola/base62/pull/12))


## [0.10.0](https://github.com/tuupola/base62/compare/0.9.0...0.10.0) - 2018-03-28

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
