<?php

declare(strict_types = 1);

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

class GmpEncoder
{
    private $options = [
        "characters" => Base62::GMP,
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);

        $uniques = count_chars($this->options["characters"], 3);
        if (62 !== strlen($uniques) || 62 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException(
                "Character set must contain 62 unique characters"
            );
        }
    }

    /**
     * Encode given data to a base62 string
     */
    public function encode(string $data): string
    {
        $hex = bin2hex($data);

        $leadZeroBytes = 0;
        while ("" !== $hex && 0 === strpos($hex, "00")) {
            $leadZeroBytes++;
            $hex = substr($hex, 2);
        }

        /* gmp_init() cannot cope with a zero-length string. */
        if ("" === $hex) {
            $base62 = str_repeat(Base62::GMP[0], $leadZeroBytes);
        } else {
            $base62 = str_repeat(Base62::GMP[0], $leadZeroBytes) . gmp_strval(gmp_init($hex, 16), 62);
        }

        if (Base62::GMP === $this->options["characters"]) {
            return $base62;
        }

        return strtr($base62, Base62::GMP, $this->options["characters"]);
    }

    /**
     * Decode given a base62 string back to data
     */
    public function decode(string $data): string
    {
        $this->validateInput($data);

        if (Base62::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base62::GMP);
        }

        $leadZeroBytes = 0;
        while ("" !== $data && 0 === strpos($data, Base62::GMP[0])) {
            $leadZeroBytes++;
            $data = substr($data, 1);
        }

        /* gmp_init() cannot cope with a zero-length string. */
        if ("" === $data) {
            return str_repeat("\x00", $leadZeroBytes);
        }

        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        return (string) hex2bin(str_repeat("00", $leadZeroBytes) . $hex);
    }

    /**
     * Encode given integer to a base62 string
     */
    public function encodeInteger(int $data): string
    {
        $base62 = gmp_strval(gmp_init($data, 10), 62);

        if (Base62::GMP === $this->options["characters"]) {
            return $base62;
        }

        return strtr($base62, Base62::GMP, $this->options["characters"]);
    }

    /**
     * Decode given base62 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        $this->validateInput($data);

        if (Base62::GMP !== $this->options["characters"]) {
            $data = strtr($data, $this->options["characters"], Base62::GMP);
        }

        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        return (int) hexdec($hex);
    }

    private function validateInput(string $data): void
    {
        /* If the data contains characters that aren't in the character set. */
        if (strlen($data) !== strspn($data, $this->options["characters"])) {
            $valid = str_split($this->options["characters"]);
            $invalid = str_replace($valid, "", $data);
            $invalid = count_chars($invalid, 3);

            throw new InvalidArgumentException(
                "Data contains invalid characters \"{$invalid}\""
            );
        }
    }
}
