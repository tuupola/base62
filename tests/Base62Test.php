<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base62
 *
 */

namespace Tuupola\Base62;

use Tuupola\Base62;

class Base62Test extends \PHPUnit_Framework_TestCase
{

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldEncodeAndDecodeRandomBytes()
    {
        $data = random_bytes(128);
        $encoded = PhpEncoder::encode($data);
        $encoded2 = GmpEncoder::encode($data);
        $encoded3 = BcmathEncoder::encode($data);
        $decoded = PhpEncoder::decode($encoded);
        $decoded2 = GmpEncoder::decode($encoded2);
        $decoded3 = BcmathEncoder::decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);
    }

    public function testShouldEncodeAndDecodeIntegers()
    {
        $data = 987654321;
        $encoded = PhpEncoder::encode($data);
        $encoded2 = GmpEncoder::encode($data);
        $encoded3 = BcmathEncoder::encode($data);
        $decoded = PhpEncoder::decode($encoded, true);
        $decoded2 = GmpEncoder::decode($encoded2, true);
        $decoded3 = BcmathEncoder::decode($encoded2, true);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);
    }

    public function testShouldEncodeAndDecodeWithLeadingZero()
    {
        $data = hex2bin("07d8e31da269bf28");
        $encoded = PhpEncoder::encode($data);
        $encoded2 = GmpEncoder::encode($data);
        $encoded3 = BcmathEncoder::encode($data);
        $decoded = PhpEncoder::decode($encoded);
        $decoded2 = GmpEncoder::decode($encoded2);
        $decoded3 = BcmathEncoder::decode($encoded3);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($decoded3, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = Base62::encode($data);
        $decoded = Base62::decode($encoded);

        $this->assertEquals($data, $decoded);
    }
}
