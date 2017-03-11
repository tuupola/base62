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

use Tuupola\Base62;
use Tuupola\Base62\PhpEncoder;
use Tuupola\Base62\BcmathEncoder;
use Tuupola\Base62\GmpEncoder;

/**
 * @BeforeMethods({"init"})
 * @Iterations(5)
 * @Warmup(2)
 * @OutputTimeUnit("seconds")
 * @OutputMode("throughput")
 */

class Base62Bench
{
    private $data;

    public function init()
    {
        $this->data = random_bytes(128);
        $this->gmp = new GmpEncoder;
        $this->gmp2 = new GmpEncoder(["characters" => Base62::INVERTED]);
        $this->php = new PhpEncoder;
        $this->bcmath = new BcmathEncoder;
    }

    /**
     * @Revs(10)
     */
    public function benchGmpEncoder()
    {
        $encoded = $this->gmp->encode($this->data);
        $decoded = $this->gmp->decode($encoded);
    }

    /**
     * @Revs(10)
     */
    public function benchGmpEncoderCustom()
    {
        $encoded = $this->gmp2->encode($this->data);
        $decoded = $this->gmp2->decode($encoded);
    }

    /**
     * @Revs(10)
     */
    public function benchPhpEncoder()
    {
        $encoded = $this->php->encode($this->data);
        $decoded = $this->php->decode($encoded);
    }

    /**
     * @Revs(10)
     */
    public function benchBcmathEncoder()
    {
        $encoded = $this->bcmath->encode($this->data);
        $decoded = $this->bcmath->decode($encoded);
    }

}