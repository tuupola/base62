<?php

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