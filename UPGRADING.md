# Updgrading from 1.x to 2.x

The `2.x` branch targets PHP 7.1 and up. All methods have their parameters and return values typehinted. Type juggling has been removed from everywhere. The `encode()` and `decode()` methods now assume string input and output. This means the following is not possible anymore.

```php
$encoded = $base62->encode(987654321);
$decoded = $base62->decode($encoded, true);
```

```php
$encoded = $base62->encode("987654321", true);
$decoded = $base62->decode($encoded, true);
```

When working with integers you should now use the implicit `encodeInteger()` and `decodeInteger()` methods instead.

```php
$encoded = $base62->encodeInteger(98765432);
$decoded = $base62->decodeInteger($encoded);
```
