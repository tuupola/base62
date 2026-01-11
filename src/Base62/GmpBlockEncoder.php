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
 * @license   https://www.opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Base62;

use Tuupola\Base62;

class GmpBlockEncoder extends BaseBlockEncoder
{
    protected function encodeBlock(string $data): string
    {
        $hex = bin2hex($data);
        $hex = ltrim($hex, "0");

        if ("" === $hex) {
            return "";
        }

        $base62 = gmp_strval(gmp_init($hex, 16), 62);

        if (Base62::GMP === $this->characters) {
            return $base62;
        }

        return strtr($base62, Base62::GMP, $this->characters);
    }

    protected function decodeBlock(string $data): string
    {
        if (Base62::GMP !== $this->characters) {
            $data = strtr($data, $this->characters, Base62::GMP);
        }

        $data = ltrim($data, Base62::GMP[0]);

        if ("" === $data) {
            return "";
        }

        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }

        return (string) hex2bin($hex);
    }
}
