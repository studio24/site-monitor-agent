<?php

namespace Studio24\Agent\Collector;

interface CollectorInterface
{
    /**
     * Return collector name
     * @return string
     */
    public function getName();

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData();
}
