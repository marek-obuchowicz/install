<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Install\Timer;

class Timer implements TimerInterface
{
    /**
     * @var array
     */
    protected $timer = [];

    /**
     * @param object $object
     *
     * @return \Spryker\Install\Timer\TimerInterface
     */
    public function start($object): TimerInterface
    {
        $this->timer[spl_object_hash($object)] = microtime(true);

        return $this;
    }

    /**
     * @param object $object
     *
     * @return float
     */
    public function end($object): float
    {
        $start = $this->timer[spl_object_hash($object)];
        $runtime = microtime(true) - $start;

        return round($runtime, 2);
    }
}
