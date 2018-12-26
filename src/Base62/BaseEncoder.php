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

abstract class BaseEncoder
{
    private $options = [
        "characters" => Base62::GMP,
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);

        $uniques = count_chars($this->options["characters"], 3);
        if (62 !== strlen($uniques) || 62 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException("Character set must contain 62 unique characters");
        }
    }

    /**
     * Encode given data to a base62 string
     */
    public function encode(string $data): string
    {
        $data = str_split($data);
        $data = array_map("ord", $data);

        $leadingZeroes = 0;
        while (!empty($data) && 0 === $data[0]) {
            $leadingZeroes++;
            array_shift($data);
        }
        $converted = $this->baseConvert($data, 256, 62);
        if (0 < $leadingZeroes) {
            $converted = array_merge(
                array_fill(0, $leadingZeroes, 0),
                $converted
            );
        }
        return implode("", array_map(function ($index) {
            return $this->options["characters"][$index];
        }, $converted));
    }

    /**
     * Decode given a base62 string back to data
     */
    public function decode(string $data): string
    {
        $this->validateInput($data);

        $data = str_split($data);
        $data = array_map(function ($character) {
            return strpos($this->options["characters"], $character);
        }, $data);

        $leadingZeroes = 0;
        while (!empty($data) && 0 === $data[0]) {
            $leadingZeroes++;
            array_shift($data);
        }

        $converted = $this->baseConvert($data, 62, 256);

        if (0 < $leadingZeroes) {
            $converted = array_merge(
                array_fill(0, $leadingZeroes, 0),
                $converted
            );
        }

        return implode("", array_map("chr", $converted));
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

    /**
     * Encode given integer to a base62 string
     */
    public function encodeInteger(int $data): string
    {
        $data = [$data];

        $converted = $this->baseConvert($data, 256, 62);

        return implode("", array_map(function ($index) {
            return $this->options["characters"][$index];
        }, $converted));
    }

    /**
     * Decode given base62 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        $this->validateInput($data);

        $data = str_split($data);
        $data = array_map(function ($character) {
            return strpos($this->options["characters"], $character);
        }, $data);

        $converted = $this->baseConvert($data, 62, 10);
        return (integer) implode("", $converted);
    }

    /**
     * Convert an integer between artbitrary bases
     */
    abstract public function baseConvert(array $source, int $sourceBase, int $targetBase): array;
}
