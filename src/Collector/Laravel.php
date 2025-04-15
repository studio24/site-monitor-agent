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
        return array_merge(
            [[
                'slug' => 'laravel',
                'version' => $this->getVersion()
            ]],
            $this->getDependencies()
        );
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

    /**
     * Lookup depenencies from composer.json
     * @return array
     */
    protected function getDependencies()
    {
        if (!$composer = file_get_contents($this->laravelBasePath . '/composer.json')) {
            return [];
        }

        $json = json_decode($composer, true);

        $dependencies = $json['require'] ?? [];

        $data = [];
        foreach ($dependencies as $name => $version) {
            $data[] = [
                'slug' => $name,
                'parent' => 'laravel',
                'version' => $version
            ];
        }

        return $data;
    }
}
