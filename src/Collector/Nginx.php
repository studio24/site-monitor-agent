<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Exec;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Nginx implements CollectorInterface
{
    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $output = Exec::exec('nginx', '-v');

        /**
         * nginx version: nginx/1.22.1
         */
        if (preg_match('!nginx/(\d+\.\d+\.\d+)!', $output, $m)) {
            $data->add('nginx', $m[1]);
        } else {
            $data->add('nginx', null, null, sprintf('Cannot determine version from output: %s', $output));
        }
        return $data;
    }
}
