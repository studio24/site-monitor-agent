<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Symfony implements CollectorInterface
{
    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $composer = new Composer('symfony');

        $data->add('symfony', $composer->getPackageVersion('symfony/framework-bundle'));
        return $data->merge($composer->collectData());
    }
}
