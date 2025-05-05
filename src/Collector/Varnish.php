<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;
use Studio24\Agent\Traits\ExecTrait;

class Varnish implements CollectorInterface
{
    use ExecTrait;

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $output = $this->exec('varnishd', '-V');

        /**
         * varnishd (varnish-7.1.1 revision 7cee1c581bead20e88d101ab3d72afb29f14d87a)
         * Copyright (c) 2006 Verdens Gang AS
         * Copyright (c) 2006-2022 Varnish Software
         */
        if (preg_match('!varnish-(\d+\.\d+\.\d+)!', $output, $m)) {
            $data->add('varnish', $m[1]);
        } else {
            $data->add('varnish', null, null, sprintf('Cannot determine version from output: %s', $output));
        }
        return $data;
    }
}
