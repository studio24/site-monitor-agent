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
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        return $this->getDependencies();
    }

    /**
     * Get installed dependencies from \Composer\InstalledVersions class.
     *
     * @param bool $includeAll
     * @return array
     */
    protected function getDependencies($includeAll = false)
    {
        // This returns all dependencies, including dependencies of dependencies.
        // Setting $includeAll to false strips out all dependencies that are not
        // mentioned in the composer.json file.

        $installedDependencies = \Composer\InstalledVersions::getInstalledPackages();
        $dependenciesFromComposerJson = $this->getDependenciesFromJson();

        $data = [];
        foreach ($installedDependencies as $name) {

            if (!$includeAll) {
                // We'll check if the installed package was mentioned in composer.json
                $include = false;
                foreach ($dependenciesFromComposerJson as $jsonDependency) {
                    if ($jsonDependency['slug'] === $name) {
                        // Skip if already in composer.json
                        $include = true;
                    }
                }

                // If it wasn't, skip it.
                if (!$include) {
                    continue;
                }
            }

            $data[] = [
                'slug' => $name,
                'parent' => $this->parentSlug,
                'version' => \Composer\InstalledVersions::getVersion($name)
            ];
        }

        var_dump($data);

        return $data;
    }

    /**
     * Lookup dependencies from composer.json
     * @return array
     */
    protected function getDependenciesFromJson()
    {
        if (!$composer = file_get_contents('composer.json')) {
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

            $data[] = [
                'slug' => $name,
                'parent' => $this->parentSlug,
                'version' => $version
            ];
        }

        return $data;
    }
}
