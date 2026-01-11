<?php

declare(strict_types = 1);

/*

Copyright (c) 2026 Mika Tuupola

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
 * @see       https://github.com/keybase/saltpack/blob/master/specs/saltpack_armor.md
 * @license   https://www.opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Base62;

use InvalidArgumentException;
use Tuupola\Base62;

abstract class BaseBlockEncoder
{
    private const LOG2_62 = 5.954196310386876;

    protected readonly int $encodedBlockSize;

    public function __construct(
        protected readonly string $characters = Base62::GMP,
        protected readonly int $blockSize = 1
    ) {
        $uniques = count_chars($characters, 3);
        /** @phpstan-ignore-next-line */
        if (62 !== strlen($uniques) || 62 !== strlen($characters)) {
            throw new InvalidArgumentException("Character set must contain 62 unique characters");
        }
        if ($blockSize < 1) {
            throw new InvalidArgumentException("Block size must be at least 1");
        }
        $this->encodedBlockSize = (int) ceil($blockSize * 8 / self::LOG2_62);
    }

    /**
     * Encode given data to a base62 string (variable width)
     */
    public function encode(string $data): string
    {
        if ("" === $data) {
            return "";
        }

        $result = "";
        $blocks = str_split($data, $this->blockSize);

        foreach ($blocks as $block) {
            $encoded = $this->encodeBlock($block);
            $blockSize = strlen($block);

            /* Variable width: calculate minimum chars needed for this block size */
            $encodedSize = (int) ceil($blockSize * 8 / self::LOG2_62);

            $result .= str_pad($encoded, $encodedSize, $this->characters[0], STR_PAD_LEFT);
        }

        return $result;
    }

    /**
     * Decode given a base62 string back to data (variable width)
     */
    public function decode(string $data): string
    {
        $this->validateInput($data);

        if ("" === $data) {
            return "";
        }

        $result = "";
        $remaining = $data;

        while ("" !== $remaining) {
            /* Full block or partial? */
            if (strlen($remaining) >= $this->encodedBlockSize) {
                $block = substr($remaining, 0, $this->encodedBlockSize);
                $remaining = substr($remaining, $this->encodedBlockSize);
                $decodedBlockSize = $this->blockSize;
            } else {
                /* Partial block: calculate decoded size from encoded size */
                $block = $remaining;
                $remaining = "";
                $decodedBlockSize = (int) floor(strlen($block) * self::LOG2_62 / 8);
            }

            $decoded = $this->decodeBlock($block);
            $result .= str_pad($decoded, $decodedBlockSize, "\x00", STR_PAD_LEFT);
        }

        return $result;
    }

    /**
     * Encode a single block as a big-endian integer
     */
    abstract protected function encodeBlock(string $data): string;

    /**
     * Decode a single block as a big-endian integer
     */
    abstract protected function decodeBlock(string $data): string;

    protected function validateInput(string $data): void
    {
        if (strlen($data) !== strspn($data, $this->characters)) {
            $valid = str_split($this->characters);
            $invalid = str_replace($valid, "", $data);
            $invalid = count_chars($invalid, 3);

            throw new InvalidArgumentException(
                /** @phpstan-ignore-next-line */
                "Data contains invalid characters \"{$invalid}\""
            );
        }
    }
}
