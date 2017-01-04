<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base62
 *
 */

use Tuupola\Base62\PhpEncoder;
use Tuupola\Base62\BcmathEncoder;
use Tuupola\Base62\GmpEncoder;

/**
 * @BeforeMethods({"init"})
 */

class Base62Bench
{
    private $data;

    public function init()
    {
        $this->data = random_bytes(128);
    }

    /**
     * @Revs(10)
     */
    public function benchGmpEncoder()
    {
        $encoded = GmpEncoder::encode($this->data);
        $decoded = GmpEncoder::decode($encoded);
    }

    /**
     * @Revs(10)
     */
    public function benchPhpEncoder()
    {
        $encoded = PhpEncoder::encode($this->data);
        $decoded = PhpEncoder::decode($encoded);
    }

    /**
     * @Revs(10)
     */
    public function benchBcmathEncoder()
    {
        $encoded = BcmathEncoder::encode($this->data);
        $decoded = BcmathEncoder::decode($encoded);
    }
}