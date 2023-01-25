<?php

namespace Studio24\Agent\Collector;

class Laravel implements CollectorInterface
{

    private $laravelBasePath = null;

     /**
     * Constructor
     * @param null $wordPressBasePath
     */
    public function __construct($laravelBasePath = null)
    {
        if ($laravelBasePath !== null) {
            $this->laravelBasePath = $laravelBasePath;
        }
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
        return [
            'version' => $this->getVersion(),
            'dependencies' => $this->getDependencies()
        ];
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
     * @return string
     */
    protected function getDependencies()
    {
        if (!$composer = file_get_contents($this->laravelBasePath . '/composer.json')) {
            return false;
        }

        $json = json_decode($composer, true);

        $dependencies = $json['require'] ?? [];

        return $dependencies;
        
    }
}
