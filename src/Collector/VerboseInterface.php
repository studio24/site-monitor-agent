<?php

namespace Studio24\Agent\Collector;

/**
 * Support verbose mode
 */
interface VerboseInterface
{
    /**
     * Set whether verbose mode is enabled
     * @param bool $verbose
     * @return mixed
     */
    public function setVerbose($verbose);

    /**
     * Is verbose mode enabled
     * @return bool
     */
    public function isVerbose();
}
