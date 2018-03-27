<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2011 Anthony Ferrara
 * Copyright (c) 2016-2018 Mika Tuupola
 *
 * Based on BaseConverter by Anthony Ferrara
 *   https://github.com/ircmaxell/SecurityLib/tree/master/lib/SecurityLib
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

class BcmathEncoder extends BaseEncoder
{
    /* http://codegolf.stackexchange.com/a/21672 */

    public function baseConvert(array $source, $source_base, $target_base)
    {
        $result = [];
        while ($count = count($source)) {
            $quotient = [];
            $remainder = 0;
            for ($i = 0; $i !== $count; $i++) {
                $accumulator = bcadd($source[$i], bcmul($remainder, $source_base));
                $digit = bcdiv($accumulator, $target_base, 0);
                $remainder = bcmod($accumulator, $target_base);
                if (count($quotient) || $digit) {
                    array_push($quotient, $digit);
                };
            }
            array_unshift($result, $remainder);
            $source = $quotient;
        }

        return $result;
    }
}
