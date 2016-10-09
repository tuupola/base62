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

use Tuupola\Base62;

class Encoder
{
    public function encode($data)
    {
        return Base62::encode($data);
    }

    public function decode($data, $integer = false)
    {
        return Base62::decode($data, $integer);
    }
}
