<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class CraftCms implements CollectorInterface
{
    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();
        $composer = new Composer('craftcms');

        $data->add('craftcms', $composer->getPackageVersion('craftcms/cms'));
        return $data->merge($composer->collectData());
    }
}
