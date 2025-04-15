<?php

namespace Studio24\Agent\Collector;

class Php implements CollectorInterface
{

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        return [
            'slug' => 'php',
            'version' => phpversion(),
        ];
    }

}
