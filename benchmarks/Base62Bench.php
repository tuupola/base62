<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2016-2021 Mika Tuupola
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
    private $gmp;
    private $php;
    private $bcmath;
    private $encoded;

    public function init()
    {
        $this->data = random_bytes(128);
        $this->gmp = new GmpEncoder;
        $this->gmp2 = new GmpEncoder(["characters" => Base62::INVERTED]);
        $this->php = new PhpEncoder;
        $this->bcmath = new BcmathEncoder;
        $this->encoded = $this->php->encode($this->data);
    }

    /**
     * @Revs(100)
     * @Groups({"encoder"})
     */
    public function benchGmpEncoder()
    {
        $encoded = $this->gmp->encode($this->data);
    }

    /**
     * @Revs(100)
     * @Groups({"encoder"})
     */
    public function benchGmpEncoderCustom()
    {
        $encoded = $this->gmp2->encode($this->data);
    }

    /**
     * @Revs(100)
     * @Groups({"encoder"})
     */
    public function benchPhpEncoder()
    {
        $encoded = $this->php->encode($this->data);
    }

    /**
     * @Revs(100)
     * @Groups({"encoder"})
     */
    public function benchBcmathEncoder()
    {
        $encoded = $this->bcmath->encode($this->data);
    }

   /**
     * @Revs(100)
     * @Groups({"decoder"})
     */
    public function benchGmpDecoder()
    {
        $encoded = $this->gmp->decode($this->encoded);
    }

    /**
     * @Revs(100)
     * @Groups({"decoder"})
     */
    public function benchGmpDecoderCustom()
    {
        $encoded = $this->gmp2->decode($this->encoded);
    }

    /**
     * @Revs(100)
     * @Groups({"decoder"})
     */
    public function benchPhpDecoder()
    {
        $encoded = $this->php->decode($this->encoded);
    }

    /**
     * @Revs(100)
     * @Groups({"decoder"})
     */
    public function benchBcmathDecoder()
    {
        $encoded = $this->bcmath->decode($this->encoded);
    }
}