<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Syluis implements CollectorInterface
{
    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $composer = new Composer('sylius');

        $data->add('sylius', $composer->getPackageVersion('sylius/sylius'));
        return $data->merge($composer->collectData());
    }
}
