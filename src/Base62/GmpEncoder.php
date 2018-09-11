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

class GmpEncoder
{
    private $options = [
        "characters" => Base62::GMP,
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);

        $uniques = count_chars($this->options["characters"], 3);
        if (62 !== strlen($uniques) || 62 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException(
                "Character set must contain 62 unique characters"
            );
        }
    }

    public function encode($data, $integer = false)
    {
        if (is_integer($data) || true === $integer) {
            $base62 = gmp_strval(gmp_init($data, 10), 62);
        } else {
            $hex = bin2hex($data);

            $leadZeroBytes = 0;
            while ("" !== $hex && 0 === strpos($hex, "00")) {
                $leadZeroBytes++;
                $hex = substr($hex, 2);
            }

            // Prior to PHP 7.0 substr() returns false
            // instead of the empty string
            if (false === $hex) {
                $hex = "";
            }

            // gmp_init() cannot cope with a zero-length string
            if ("" === $hex) {
                $base62 = str_repeat(Base62::GMP[0], $leadZeroBytes);
            } else {
                $base62 = str_repeat(Base62::GMP[0], $leadZeroBytes) . gmp_strval(gmp_init($hex, 16), 62);
            }
        }

        if (Base62::GMP === $this->options["characters"]) {
            return $base62;
        }

        return strtr($base62, Base62::GMP, $this->options["characters"]);
    }

    public function decode($data, $integer = false)
    {
        // If the data contains characters that aren't in the character set
        if (strlen($data) !== strspn($data, $this->options["characters"])) {
            throw new InvalidArgumentException("Data contains invalid characters");
        }

        if (Base62::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base62::GMP);
        }

        $leadZeroBytes = 0;
        while ("" !== $data && 0 === strpos($data, Base62::GMP[0])) {
            $leadZeroBytes++;
            $data = substr($data, 1);
        }

        // Prior to PHP 7.0 substr() returns false
        // instead of the empty string
        if (false === $data) {
            $data = "";
        }

        // gmp_init() cannot cope with a zero-length string
        if ("" === $data) {
            return str_repeat("\x00", $leadZeroBytes);
        }

        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        /* Return as integer when requested. */
        if ($integer) {
            return hexdec($hex);
        }

        return hex2bin(str_repeat("00", $leadZeroBytes) . $hex);
    }

    public function encodeInteger($data)
    {
        return $this->encode($data, true);
    }

    public function decodeInteger($data)
    {
        return $this->decode($data, true);
    }
}
