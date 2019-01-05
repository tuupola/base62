<?php

//declare(strict_types=1);

/*

Copyright (c) 2016-2018 Mika Tuupola

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
use PHPUnit\Framework\TestCase;

class Base62Test extends TestCase
{
    protected function tearDown()
    {
        Base62Proxy::$options = [
            "characters" => Base62::GMP,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base62->encodeInteger($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        // Base62Proxy::$options = [
        //     "characters" => $characters,
        // ];
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

        // Base62Proxy::$options = [
        //     "characters" => $characters,
        // ];
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

        $php = new PhpEncoder(["characters" => Base62::INVERTED]);
        $gmp = new GmpEncoder(["characters" => Base62::INVERTED]);
        $bcmath = new BcmathEncoder(["characters" => Base62::INVERTED]);
        $base62 = new Base62(["characters" => Base62::INVERTED]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => Base62::INVERTED,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded3 = $bcmath->encodeInteger($data);
        $encoded4 = $base62->encodeInteger($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

    public function testShouldThrowExceptionOnDecodeInvalidData()
    {
        $invalid = "invalid~data-%@#!@*#-foo";

        $decoders = [
            new PhpEncoder(),
            new GmpEncoder(),
            new BcmathEncoder(),
            new Base62(),
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

    public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet()
    {
        /* This would normally be valid, however the custom character set */
        /* is missing the T character. */
        $invalid = "T8dgcjRGuYUueWht";
        $options = [
            "characters" => "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRS-UVWXYZ"
        ];

        $decoders = [
            new PhpEncoder($options),
            new GmpEncoder($options),
            new BcmathEncoder($options),
            new Base62($options),
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
        $options = [
            "characters" => "0023456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        ];

        $decoders = [
            PhpEncoder::class,
            GmpEncoder::class,
            BcmathEncoder::class,
            Base62::class,
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($options);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }

        $options = [
            "characters" => "00123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        ];


        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($options);
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $bcmath = new BcmathEncoder(["characters" => $characters]);
        $base62 = new Base62(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded3 = $bcmath->encode($data);
        $encoded4 = $base62->encode($data);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
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

    public function characterSetProvider()
    {
        return [
            "GMP character set" => [Base62::GMP],
            "inverted character set" => [Base62::INVERTED],
            "custom character set" => ["1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"],
        ];
    }
}
