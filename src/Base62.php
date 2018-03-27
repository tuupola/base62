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

namespace Tuupola;

class Base62
{
    const GMP = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    const INVERTED = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    private $encoder;
    private $options = [];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
        if (function_exists("gmp_init")) {
            $this->encoder = new Base62\GmpEncoder($this->options);
        } else {
            $this->encoder = new Base62\PhpEncoder($this->options);
        }
    }

    public function encode($data, $integer = false)
    {
        return $this->encoder->encode($data, $integer);
    }

    public function decode($data, $integer = false)
    {
        return $this->encoder->decode($data, $integer);
    }

    public function encodeInteger($data)
    {
        return $this->encoder->encodeInteger($data);
    }

    public function decodeInteger($data)
    {
        return $this->encoder->decodeInteger($data);
    }
}
