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

class GmpEncoder
{
    public static function encode($data)
    {
        $hex = bin2hex($data);
        return gmp_strval(gmp_init($hex, 16), 62);
    }

    public static function decode($data)
    {
        $hex = gmp_strval(gmp_init($data, 62), 16);
        if (strlen($hex) % 2) {
            $hex = "0" . $hex;
        }
        return hex2bin($hex);
    }
}
