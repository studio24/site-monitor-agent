<?php

namespace Studio24\Agent\Collector;

/**
 * Support setting URL and environment from collector, return null if data is not returned
 */
interface ApplicationInterface
{
    /**
     * Return website URL
     * @return string|null
     */
    public function getUrl();

    /**
     * Return website environment
     * @return string|null
     */
    public function getEnvironment();
}