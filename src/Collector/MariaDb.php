<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;
use Studio24\Agent\Traits\ExecTrait;

class MariaDb implements CollectorInterface
{
    use ExecTrait;

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $output = $this->exec('mariadbd', '-V');

        /**
         * /usr/sbin/mariadbd  Ver 10.11.11-MariaDB-0+deb12u1 for debian-linux-gnu on x86_64 (Debian 12)
         */
        if (preg_match('!Ver (\d+\.\d+\.\d+)-MariaDB!', $output, $m)) {
            $data->add('mariadb', $m[1]);
        } else {
            $data->add('mariadb', null, null, sprintf('Cannot determine version from output: %s', $output));
        }
        return $data;
    }
}
