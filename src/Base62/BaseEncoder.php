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

abstract class BaseEncoder
{
    private $options = [
        "characters" => Base62::GMP,
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);

        $uniques = count_chars($this->options["characters"], 3);
        if (62 !== strlen($uniques) || 62 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException("Character set must contain 62 unique characters");
        }
    }

    public function encode($data, $integer = false)
    {
        if (is_integer($data) || true === $integer) {
            $data = [$data];
        } else {
            $data = str_split($data);
            $data = array_map(function ($character) {
                return ord($character);
            }, $data);
        }

        $converted = $this->baseConvert($data, 256, 62);

        return implode("", array_map(function ($index) {
            return $this->options["characters"][$index];
        }, $converted));
    }

    public function decode($data, $integer = false)
    {
        // If the data contains characters that aren't in the character set
        if (strlen($data) !== strspn($data, $this->options["characters"])) {
            throw new InvalidArgumentException("Data contains invalid characters");
        }

        $data = str_split($data);
        $data = array_map(function ($character) {
            return strpos($this->options["characters"], $character);
        }, $data);

        /* Return as integer when requested. */
        if ($integer) {
            $converted = $this->baseConvert($data, 62, 10);
            return (integer) implode("", $converted);
        }

        $converted = $this->baseConvert($data, 62, 256);

        return implode("", array_map(function ($ascii) {
            return chr($ascii);
        }, $converted));
    }

    public function encodeInteger($data)
    {
        return $this->encode($data, true);
    }

    public function decodeInteger($data)
    {
        return $this->decode($data, true);
    }

    abstract public function baseConvert(array $source, $source_base, $target_base);
}
