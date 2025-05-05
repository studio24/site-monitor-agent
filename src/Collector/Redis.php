<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;
use Studio24\Agent\Traits\ExecTrait;

class Redis implements CollectorInterface
{
    use ExecTrait;

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $output = $this->exec('redis-server', '-v');

        /**
         * Redis server v=7.0.15 sha=00000000:0 malloc=jemalloc-5.3.0 bits=64 build=c89c70d1d28059e4
         */
        if (preg_match('!Redis server v=(\d+\.\d+\.\d+)!', $output, $m)) {
            $data->add('redis', $m[1]);
        } else {
            $data->add('redis', null, null, sprintf('Cannot determine version from output: %s', $output));
        }
        return $data;
    }
}
