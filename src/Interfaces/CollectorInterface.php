<?php

namespace Studio24\Agent\Interfaces;

use Studio24\Agent\Model\VersionCollection;

/**
 * Base data collector interface
 */
interface CollectorInterface
{
    /**
     * Collect data
     * @return VersionCollection Collection of one or more tech version data
     */
    public function collectData();
}
