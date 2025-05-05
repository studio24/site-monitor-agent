<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Laravel implements CollectorInterface
{
    /** @var Composer */
    protected $composer;

     /**
     * Constructor
     * @param null $basePath
     */
    public function __construct($basePath = null)
    {
        $this->composer = new Composer($basePath, 'laravel');
    }

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $data = new VersionCollection();

        $data->add('laravel', $this->composer->getPackageVersion('laravel/framework'));
        $this->composer->exclude('laravel/framework');
        return $data->merge($this->composer->collectData());
    }
}
