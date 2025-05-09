<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Exec;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Apache implements CollectorInterface
{
    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        Exec::exec('apache2', '-v');

        /**
         * Server version: Apache/2.4.62 (Debian)
         * Server built:   2024-10-04T15:21:08
         */
        if (preg_match('!Apache/(\d+\.\d+\.\d+)!', $output, $m)) {
            $data->add('apache', $m[1]);
        } else {
            $data->add('apache', null, null, sprintf('Cannot determine version from output: %s', $output));
        }

        return $data;
    }
}
