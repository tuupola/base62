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

namespace Tuupola;

class Base62
{
    public static function encode($data)
    {
        if (function_exists("gmp_init")) {
            return Base62\GmpEncoder::encode($data);
        }
        return Base62\PhpEncoder::encode($data);
    }

    public static function decode($data, $integer = false)
    {
        if (function_exists("gmp_init")) {
            return Base62\GmpEncoder::decode($data, $integer);
        }
        return Base62\PhpEncoder::decode($data, $integer);
    }
}
