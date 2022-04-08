<?php

namespace Studio24\Agent;

use Studio24\Agent\Collector\CollectorInterface;

class Agent
{
    /** @var CollectorInterface[] */
    private $collectors = [];

    public function __construct(array $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * Return array of data collected for this site
     * @return array
     */
    public function collectData()
    {
        $data = [];

        /** @var CollectorInterface $collector */
        foreach ($this->collectors as $collector) {
            $data[$collector->getName()] = $collector->collectData();
        }

        return $data;
    }

}