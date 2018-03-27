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

use Tuupola\Base62;

class Base62Proxy
{
    public static $options = [
        "characters" => Base62::GMP,
    ];

    public static function encode($data, $integer = false, $options = [])
    {
        return (new Base62(self::$options))->encode($data, $integer);
    }

    public static function decode($data, $integer = false, $options = [])
    {
        return (new Base62(self::$options))->decode($data, $integer);
    }

    public static function encodeInteger($data, $options = [])
    {
        return (new Base62(self::$options))->encodeInteger($data);
    }

    public static function decodeInteger($data, $options = [])
    {
        return (new Base62(self::$options))->decodeInteger($data);
    }
}
