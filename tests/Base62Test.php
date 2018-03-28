<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2016-2018 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base62
 *
 */

namespace Tuupola\Base62;

use InvalidArgumentException;
use Tuupola\Base62;
use Tuupola\Base62Proxy;

class Base62Test extends \PHPUnit_Framework_TestCase
{

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldEncodeAndDecodeRandomBytes()
    {
        $data = random_bytes(128);
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $decoded3 = (new BcmathEncoder)->decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldEncodeAndDecodeIntegers()
    {
        $data = 987654321;
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded, true);
        $decoded2 = (new GmpEncoder)->decode($encoded2, true);
        $decoded3 = (new BcmathEncoder)->decode($encoded2, true);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4, true);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5, true);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldEncodeAndDecodeStringsAsIntegers()
    {
        $data = "987654321";
        $encoded = (new PhpEncoder)->encodeInteger($data);
        $encoded2 = (new GmpEncoder)->encodeInteger($data);
        $encoded3 = (new BcmathEncoder)->encodeInteger($data);
        $decoded = (new PhpEncoder)->decodeInteger($encoded);
        $decoded2 = (new GmpEncoder)->decodeInteger($encoded2);
        $decoded3 = (new BcmathEncoder)->decodeInteger($encoded2);

        $this->assertEquals($encoded, (new Base62)->encode(987654321));

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encodeInteger($data);
        $decoded4 = (new Base62)->decodeInteger($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base62Proxy::encodeInteger($data);
        $decoded5 = Base62Proxy::decodeInteger($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base62)->encode($data);
        $decoded = (new Base62)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    public function testShouldEncodeAndDecodeWithLeadingZero()
    {
        $data = hex2bin("07d8e31da269bf28");
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $decoded3 = (new BcmathEncoder)->decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldUseDefaultCharacterSet()
    {
        $data = "Hello world!";

        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $decoded3 = (new BcmathEncoder)->decode($encoded2);

        $this->assertEquals($encoded, "T8dgcjRGuYUueWht");
        $this->assertEquals($encoded2, "T8dgcjRGuYUueWht");
        $this->assertEquals($encoded3, "T8dgcjRGuYUueWht");
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5);
        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldUseInvertedCharacterSet()
    {
        $data = "Hello world!";

        $encoded = (new PhpEncoder(["characters" => Base62::INVERTED]))->encode($data);
        $encoded2 = (new GmpEncoder(["characters" => Base62::INVERTED]))->encode($data);
        $encoded3 = (new BcmathEncoder(["characters" => Base62::INVERTED]))->encode($data);
        $decoded = (new PhpEncoder(["characters" => Base62::INVERTED]))->decode($encoded);
        $decoded2 = (new GmpEncoder(["characters" => Base62::INVERTED]))->decode($encoded2);
        $decoded3 = (new BcmathEncoder(["characters" => Base62::INVERTED]))->decode($encoded2);

        $this->assertEquals($encoded, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($encoded2, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($encoded3, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        Base62Proxy::$options = [
            "characters" => Base62::INVERTED,
        ];
        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5);
        $this->assertEquals($encoded5, "t8DGCJrgUyuUEwHT");
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldUseICustomCharacterSet()
    {
        $data = "Hello world!";
        $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        $encoded = (new PhpEncoder(["characters" => $characters]))->encode($data);
        $encoded2 = (new GmpEncoder(["characters" => $characters]))->encode($data);
        $encoded3 = (new BcmathEncoder(["characters" => $characters]))->encode($data);
        $decoded = (new PhpEncoder(["characters" => $characters]))->decode($encoded);
        $decoded2 = (new GmpEncoder(["characters" => $characters]))->decode($encoded2);
        $decoded3 = (new BcmathEncoder(["characters" => $characters]))->decode($encoded2);

        $this->assertEquals($encoded, "DiNQMTBq4IE4OGR3");
        $this->assertEquals($encoded2, "DiNQMTBq4IE4OGR3");
        $this->assertEquals($encoded3, "DiNQMTBq4IE4OGR3");
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4);
        $this->assertEquals($data, $decoded4);

        Base62Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5);
        $this->assertEquals($encoded5, "DiNQMTBq4IE4OGR3");
        $this->assertEquals($data, $decoded5);
    }

    public function testShouldEncodeAndDecodeBigIntegers()
    {
        $data = PHP_INT_MAX;
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $encoded3 = (new BcmathEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded, true);
        $decoded2 = (new GmpEncoder)->decode($encoded2, true);
        $decoded3 = (new BcmathEncoder)->decode($encoded2, true);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);

        $encoded4 = (new Base62)->encode($data);
        $decoded4 = (new Base62)->decode($encoded4, true);
        $this->assertEquals($data, $decoded4);

        Base62Proxy::$options = [
            "characters" => Base62::GMP,
        ];
        $encoded5 = Base62Proxy::encode($data);
        $decoded5 = Base62Proxy::decode($encoded5, true);

        $this->assertEquals($encoded, $encoded5);
        $this->assertEquals($data, $decoded5);
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
}
