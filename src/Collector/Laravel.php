<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;

class Laravel implements CollectorInterface
{
    private $laravelBasePath = null;

     /**
     * Constructor
     * @param null $wordPressBasePath
     */
    public function __construct($laravelBasePath = null)
    {
        $this->laravelBasePath = $laravelBasePath;
    }

    /**
     * Return collector name
     * @return string
     */
    public function getName()
    {
        return 'Laravel';
    }

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        $core = [];
        $core[] = [
            'slug' => 'laravel',
            'version' => $this->getVersion()
        ];

        $composer = new Composer($this->laravelBasePath, 'laravel');

        return array_merge($core, $composer->collectData());
    }

    /**
     * Lookup Laravel version from Application.php
     * @return string
     */
    protected function getVersion()
    {
        $app = file_get_contents($this->laravelBasePath . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php');

        if (preg_match("/VERSION = '(.+?)';/", $app, $m) && $m[1]) {
            $version = $m[1];
        } else {
            $version = 'N/A';
        }

        return $version;

    }

}
