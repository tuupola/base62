<?php

declare(strict_types=1);

/*

Copyright (c) 2016-2025 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/base62
 * @license   https://www.opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Base62;

use InvalidArgumentException;
use Tuupola\Base62;
use Tuupola\Base62Proxy;
use Tuupola\Base62\PhpBlockEncoder;
use Tuupola\Base62\GmpBlockEncoder;
use Tuupola\Base62\BcmathBlockEncoder;
use PHPUnit\Framework\TestCase;

class Base62Test extends TestCase
{
    protected function tearDown(): void
    {
        Base62Proxy::$characters = Base62::GMP;
        Base62Proxy::$blockSize = 0;
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeRandomBytes($characters)
    {
        $data = random_bytes(128);

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeIntegers($characters)
    {
        $data = 987654321;

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base62->encodeInteger($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $bcmath->decodeInteger($encoded3));
        $this->assertEquals($data, $base62->decodeInteger($encoded4));
        $this->assertEquals($data, Base62Proxy::decodeInteger($encoded5));
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base62)->encode($data);
        $decoded = (new Base62)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeWithLeadingZero($characters)
    {
        $data = hex2bin("07d8e31da269bf28");

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    public function testShouldUseDefaultCharacterSet()
    {
        $data = "Hello world!";

        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $bcmath = new BcmathEncoder();
        $base62 = new Base62();

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        // Base62Proxy::$characters = => $characters,

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded, "T8dgcjRGuYUueWht");
        $this->assertEquals($encoded2, "T8dgcjRGuYUueWht");
        $this->assertEquals($encoded3, "T8dgcjRGuYUueWht");
        $this->assertEquals($encoded4, "T8dgcjRGuYUueWht");
        $this->assertEquals($encoded5, "T8dgcjRGuYUueWht");

        $data = hex2bin("0000010203040506");
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        // Base62Proxy::$characters = => $characters,

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded, "00JVb3WII");
        $this->assertEquals($encoded2, "00JVb3WII");
        $this->assertEquals($encoded3, "00JVb3WII");
        $this->assertEquals($encoded4, "00JVb3WII");
        $this->assertEquals($encoded5, "00JVb3WII");
    }

    public function testShouldUseInvertedCharacterSet()
    {
        $data = "Hello world!";

        $php = new PhpEncoder(Base62::INVERTED);
        $gmp = new GmpEncoder(Base62::INVERTED);
        $bcmath = new BcmathEncoder(Base62::INVERTED);
        $base62 = new Base62(Base62::INVERTED);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = Base62::INVERTED;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($encoded2, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($encoded3, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($encoded4, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($encoded5, "t8DGCJrgUyuUEwHT");

        $data = hex2bin("0000010203040506");

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);
        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded, "00jvB3wii");
        $this->assertEquals($encoded2, "00jvB3wii");
        $this->assertEquals($encoded3, "00jvB3wii");
        $this->assertEquals($encoded4, "00jvB3wii");
        $this->assertEquals($encoded5, "00jvB3wii");
    }

    public function testShouldUseCustomCharacterSet()
    {
        $data = "Hello world!";
        $characters = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded, "t9DGCJrgUyuUEwHT");
        $this->assertEquals($encoded2, "t9DGCJrgUyuUEwHT");
        $this->assertEquals($encoded3, "t9DGCJrgUyuUEwHT");
        $this->assertEquals($encoded4, "t9DGCJrgUyuUEwHT");
        $this->assertEquals($encoded5, "t9DGCJrgUyuUEwHT");

        $data = hex2bin("0000010203040506");

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);
        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded, "11jvB4wii");
        $this->assertEquals($encoded2, "11jvB4wii");
        $this->assertEquals($encoded3, "11jvB4wii");
        $this->assertEquals($encoded4, "11jvB4wii");
        $this->assertEquals($encoded5, "11jvB4wii");
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeBigIntegers($characters)
    {
        $data = PHP_INT_MAX;

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base62->encodeInteger($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $bcmath->decodeInteger($encoded3));
        $this->assertEquals($data, $base62->decodeInteger($encoded4));
        $this->assertEquals($data, Base62Proxy::decodeInteger($encoded5));
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeInvalidData($encoder)
    {
        $this->expectException(InvalidArgumentException::class);

        $encoder->decode("invalid~data-%@#!@*#-foo", false);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnEncodeNegativeInteger($encoder)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot encode negative integer");

        $encoder->encodeInteger(-1);
    }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeIntegerInvalidData($encoder)
    {
        $this->expectException(InvalidArgumentException::class);

        $encoder->decodeInteger("invalid~data-%@#!@*#-foo");
    }

    public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet()
    {
        /* This would normally be valid, however the custom character set */
        /* is missing the T character. */
        $invalid = "T8dgcjRGuYUueWht";
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRS-UVWXYZ";

        $decoders = [
            new PhpEncoder($characters),
            new GmpEncoder($characters),
            new BcmathEncoder($characters),
            new Base62($characters),
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                $decoder->decode($invalid, false);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }
    }

    public function testShouldThrowExceptionWithInvalidCharacterSet()
    {
        $characters = "0023456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        $decoders = [
            PhpEncoder::class,
            GmpEncoder::class,
            BcmathEncoder::class,
            Base62::class,
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($characters);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }

        $characters = "00123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";


        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($characters);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroByte($characters)
    {
        $data = "\x00";

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytes($characters)
    {
        $data = "\x00\x00\x00";

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroBytePrefix($characters)
    {
        $data = "\x00\x01\x02";

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;
        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytePrefix($characters)
    {
        $data = "\x00\x00\x00\x01\x02";

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;

        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeZeroInteger($characters)
    {
        $data = 0;

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base62->encodeInteger($data);

        Base62Proxy::$characters = $characters;
        $encoded5 = Base62Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $bcmath->decodeInteger($encoded3));
        $this->assertEquals($data, $base62->decodeInteger($encoded4));
        $this->assertEquals($data, Base62Proxy::decodeInteger($encoded5));
    }

    /**
     * @dataProvider characterSetProvider
     */
    public function testShouldEncodeAndDecodeEmptyString($characters)
    {
        $data = "";

        $php = new PhpEncoder($characters);
        $gmp = new GmpEncoder($characters);
        $bcmath = new BcmathEncoder($characters);
        $base62 = new Base62($characters);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$characters = $characters;
        $encoded5 = Base62Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
        $this->assertEquals($data, Base62Proxy::decode($encoded5));
    }

    /**
     * @dataProvider singleByteProvider
     */
    public function testShouldEncodeAndDecodeSingleByte($byte)
    {
        $data = chr($byte);

        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $bcmath = new BcmathEncoder();
        $base62 = new Base62();

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded3, $encoded);
        $this->assertEquals($encoded4, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $bcmath->decode($encoded3));
        $this->assertEquals($data, $base62->decode($encoded4));
    }

    public function encoderProvider()
    {
        return [
            "PhpEncoder" => [new PhpEncoder()],
            "GmpEncoder" => [new GmpEncoder()],
            "BcmathEncoder" => [new BcmathEncoder()],
            "Base62" => [new Base62()],
        ];
    }

    public function singleByteProvider()
    {
        $bytes = [];
        for ($i = 0; $i <= 255; $i++) {
            $bytes[sprintf("0x%02X", $i)] = [$i];
        }
        return $bytes;
    }

    public function characterSetProvider()
    {
        return [
            "GMP character set" => [Base62::GMP],
            "inverted character set" => [Base62::INVERTED],
            "custom character set" => ["1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"],
        ];
    }

    /**
     * Test vectors from base62.js
     * @see https://github.com/therootcompany/base62.js/issues/1
     */
    public function base62JsTestVectorProvider()
    {
        return [
            "Hello, 世界 (UTF-8)" => [
                "Hello, 世界",
                "1wJfrzvdbuFbL65vcS",
            ],
            "Hello World (ASCII)" => [
                "Hello World",
                "73XpUgyMwkGr29M",
            ],
            "Mixed null and max bytes" => [
                "\x00\x00\x00\x00\xff\xff\xff\xff",
                "000004gfFC3",
            ],
            "Reversed pattern" => [
                "\xff\xff\xff\xff\x00\x00\x00\x00",
                "LygHZwPV2MC",
            ],
        ];
    }

    /**
     * @dataProvider base62JsTestVectorProvider
     * @see https://github.com/therootcompany/base62.js/issues/1
     */
    public function testShouldMatchBase62JsTestVectors($data, $expected)
    {
        $php = new PhpBlockEncoder(Base62::GMP, 32);
        $gmp = new GmpBlockEncoder(Base62::GMP, 32);
        $bcmath = new BcmathBlockEncoder(Base62::GMP, 32);
        $base62 = new Base62(Base62::GMP, 32);

        $this->assertEquals($expected, $php->encode($data));
        $this->assertEquals($expected, $gmp->encode($data));
        $this->assertEquals($expected, $bcmath->encode($data));
        $this->assertEquals($expected, $base62->encode($data));

        $this->assertEquals($data, $php->decode($expected));
        $this->assertEquals($data, $gmp->decode($expected));
        $this->assertEquals($data, $bcmath->decode($expected));
        $this->assertEquals($data, $base62->decode($expected));
    }

    public function ksuidTestVectorProvider()
    {
        return [
            "KSUID example" => [
                "066A029C73FC1AA3B2446246D6E89FCD909E8FE8",
                "0ujzPyRiIAffKhBux4PvQdDqMHY",
            ],
            "KSUID min" => [
                "0000000000000000000000000000000000000000",
                "000000000000000000000000000",
            ],
            "KSUID max" => [
                "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                "aWgEPTl1tmebfsQzFP4bxwgy80V",
            ],
        ];
    }

    /**
     * @dataProvider ksuidTestVectorProvider
     */
    public function testShouldMatchKsuidTestVectors($hex, $expected)
    {
        $data = hex2bin($hex);

        $php = new PhpBlockEncoder(Base62::GMP, 20);
        $gmp = new GmpBlockEncoder(Base62::GMP, 20);
        $bcmath = new BcmathBlockEncoder(Base62::GMP, 20);
        $base62 = new Base62(Base62::GMP, 20);

        $this->assertEquals($expected, $php->encode($data));
        $this->assertEquals($expected, $gmp->encode($data));
        $this->assertEquals($expected, $bcmath->encode($data));
        $this->assertEquals($expected, $base62->encode($data));

        $this->assertEquals($data, $php->decode($expected));
        $this->assertEquals($data, $gmp->decode($expected));
        $this->assertEquals($data, $bcmath->decode($expected));
        $this->assertEquals($data, $base62->decode($expected));
    }

    public function saltpackTestVectorProvider()
    {
        return [
            "min (all zeros)" => [
                "0000000000000000000000000000000000000000000000000000000000000000",
                "0000000000000000000000000000000000000000000",
            ],
            "max (all 0xFF)" => [
                "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                "yhjskwdA6OZ1AL1YmHWZWm8LLG7HjnuCA2j5rOw8Xp1",
            ],
            "0x01 at start" => [
                "0100000000000000000000000000000000000000000000000000000000000000",
                "0EhWuMzfS7MPuxAu520Mu4XwCuyZfalRej3Z8gTlzA8",
            ],
            "0x01 at end" => [
                "0000000000000000000000000000000000000000000000000000000000000001",
                "0000000000000000000000000000000000000000001",
            ],
            "alternating 0x00 0xFF" => [
                "00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF",
                "0Edz0QHWMhWDkqBpHJblBDwAhGSycXFAu0UZU7ZgjU7",
            ],
            "alternating 0xFF 0x00" => [
                "FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00FF00",
                "yT5tkWLdjh2nPUpjUxuoLYCAdzeJ7Gf1G2EWNHMRoKu",
            ],
            "ascending 0x00-0x1F" => [
                "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F",
                "003aUlTJC7tjlCTQj2uNU3MFagCXG9LRKRcwGkBIDlf",
            ],
            "descending 0x1F-0x00" => [
                "1F1E1D1C1B1A191817161514131211100F0E0D0C0B0A09080706050403020100",
                "7NUg80V82zhpOJzCktNvQChgC6CAxRM6U8CUhTn1IIq",
            ],
            "all 0x55" => [
                "5555555555555555555555555555555555555555555555555555555555555555",
                "KEZxaJXihSr0O70WG5qBqG2mRkhlFGdOigF1x8JNVwL",
            ],
            "all 0xAA" => [
                "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
                "eT9vAd5ROvi0mE12WBgNgW5YtVPWUXGnRMU3uGcl1sg",
            ],
            "first half 0x00 second 0xFF" => [
                "00000000000000000000000000000000FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF",
                "0000000000000000000007n42DGM5Tflk9n8mt7Fhc7",
            ],
            "first half 0xFF second 0x00" => [
                "FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF00000000000000000000000000000000",
                "yhjskwdA6OZ1AL1YmHWZWeLHJ2qveKEQPsvx4VosqCu",
            ],
            "0xFF at start" => [
                "FF00000000000000000000000000000000000000000000000000000000000000",
                "yT2LqZdUeHCbFNqehFWCchaP8L8i4D8kVJfWiiSMYeu",
            ],
            "0xFF at end" => [
                "00000000000000000000000000000000000000000000000000000000000000FF",
                "0000000000000000000000000000000000000000047",
            ],
            "DEADBEEF repeated" => [
                "DEADBEEFDEADBEEFDEADBEEFDEADBEEFDEADBEEFDEADBEEFDEADBEEFDEADBEEF",
                "qnqUDILfbk3NCC7vxvHkREe9pHE2yEo82btf7NAUd15",
            ],
            "powers of 2" => [
                "0102040810204080010204081020408001020408102040800102040810204080",
                "0EohuKFHrEhFnG3di03eMYuQYvWADsM0zJM3rltF5VI",
            ],
            "all 0x80" => [
                "8080808080808080808080808080808080808080808080808080808080808080",
                "UTFUe65YURP79K2F1cha4IKEbPq2a7XncpwNMfh2aQa",
            ],
            "all 0x7F" => [
                "7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F7F",
                "UEUO6qXbbx9u10zJkeozSTo6jqHF9gMOXCmiUjF5xOR",
            ],
            "increment by 8" => [
                "0008101820283038404850586068707880889098A0A8B0B8C0C8D0D8E0E8F0F8",
                "00Shy7mTZ1Bu5bnRoNH1sQs0jRcI5ClWdZ1W9xSLm9I",
            ],
            "Hello World padded" => [
                "48656C6C6F20576F726C64210000000000000000000000000000000000000000",
                "HANLrIgIWPomzbqJv7smnL7lSvGNR0CfHOcpVlolAJs",
            ],
        ];
    }

    /**
     * @dataProvider saltpackTestVectorProvider
     * @see https://github.com/keybase/saltpack
     */
    public function testShouldMatchSaltpackTestVectors($hex, $expected)
    {
        $data = hex2bin($hex);

        $php = new PhpBlockEncoder(Base62::GMP, 32);
        $gmp = new GmpBlockEncoder(Base62::GMP, 32);
        $bcmath = new BcmathBlockEncoder(Base62::GMP, 32);
        $base62 = new Base62(Base62::GMP, 32);

        $this->assertEquals($expected, $php->encode($data));
        $this->assertEquals($expected, $gmp->encode($data));
        $this->assertEquals($expected, $bcmath->encode($data));
        $this->assertEquals($expected, $base62->encode($data));

        $this->assertEquals($data, $php->decode($expected));
        $this->assertEquals($data, $gmp->decode($expected));
        $this->assertEquals($data, $bcmath->decode($expected));
        $this->assertEquals($data, $base62->decode($expected));
    }
}
