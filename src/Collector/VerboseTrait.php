<?php

namespace Studio24\Agent\Collector;

trait VerboseTrait
{
    private $verbose = false;

    /**
     * Set whether verbose mode is enabled
     * @param bool $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = (bool) $verbose;
    }

    /**
     * Is verbose mode enabled
     * @return bool
     */
    public function isVerbose()
    {
        return $this->verbose;
    }
}