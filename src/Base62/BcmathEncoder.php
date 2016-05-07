<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2011-2016 Anthony Ferrara, Mika Tuupola
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

class BcmathEncoder
{
    public static $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    public static function encode($data)
    {
        if (is_integer($data)) {
            $data = [$data];
        } else {
            $data = str_split($data);
            $data = array_map(function ($character) {
                return ord($character);
            }, $data);
        }

        $converted = self::baseConvert($data, 256, 62);

        return implode("", array_map(function ($index) {
            return self::$characters[$index];
        }, $converted));
    }

    public static function decode($data, $integer = false)
    {
        $data = str_split($data);
        $data = array_map(function ($character) {
            return strpos(self::$characters, $character);
        }, $data);

        /* Return as integer when requested. */
        if ($integer) {
            $converted = self::baseConvert($data, 62, 10);
            return (integer) implode("", $converted);
        }

        $converted = self::baseConvert($data, 62, 256);

        return implode("", array_map(function ($ascii) {
            return chr($ascii);
        }, $converted));
    }

    /* http://codegolf.stackexchange.com/a/21672 */

    public static function baseConvert(array $source, $source_base, $target_base)
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
