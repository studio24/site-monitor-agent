<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Interfaces\CollectorInterface;

class Composer implements CollectorInterface
{

    private $basePath = null;

    private $parentSlug = null;

    /**
     * Constructor
     * @param null $basePath
     */
    public function __construct($basePath = null, $parentSlug = null)
    {
        $this->basePath = $basePath;
        $this->parentSlug = $parentSlug;
    }

    /**
     * Return collector name
     * @return string
     */
    public function getName()
    {
        return 'Composer';
    }

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        return $this->getDependencies();
    }

    /**
     * Lookup dependencies from composer.json
     * @return array
     */
    protected function getDependencies()
    {
        if (!$composer = file_get_contents($this->basePath . '/composer.json')) {
            return [];
        }

        $json = json_decode($composer, true);

        $dependencies = $json['require'] ?? [];

        $data = [];
        foreach ($dependencies as $name => $version) {

            // Require something/something format.
            // Excludes "php" version dependency which is not a package.
            if (!preg_match('/^.*?\/.*?$/', $name, $matches)) {
                continue;
            }

//            $version = 'N/A';
//            if (\Composer\InstalledVersions::isInstalled($name, false)) {
//                $version = \Composer\InstalledVersions::getVersion($name);
//            }

            $data[] = [
                'slug' => $name,
                'parent' => $this->parentSlug,
                'version' => $version
            ];
        }

        return $data;
    }
}
