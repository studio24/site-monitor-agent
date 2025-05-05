<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;
use Studio24\Agent\Traits\ExecTrait;

class MySQL implements CollectorInterface
{
    use ExecTrait;

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $output = $this->exec('mysql', '-V');

        /**
         * mysql  Ver 14.14 Distrib 5.7.44, for Linux (x86_64) using  EditLine wrapper
         */
        if (preg_match('!Distrib (\d+\.\d+\.\d+)-MariaDB!', $output, $m)) {
            $data->add('mysql', $m[1]);
        } else {
            $data->add('mysql', null, null, sprintf('Cannot determine version from output: %s', $output));
        }
        return $data;
    }
}
