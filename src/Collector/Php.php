<?php

namespace Studio24\Agent\Collector;

class Php implements CollectorInterface
{
    /**
     * Return collector name
     * @return string
     */
    public function getName()
    {
        return 'PHP';
    }

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        return [
            'version' => phpversion(),
            'extensions' => get_loaded_extensions()
        ];
    }
}
