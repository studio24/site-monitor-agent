<?php

namespace Studio24\Agent\Collector;

use Composer\InstalledVersions;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Composer implements CollectorInterface
{
    /** @var string|null */
    private $parentSlug = null;

    /** @var VersionCollection */
    private $data;

    private $basePath = './';
    private $exclude = [];

    /**
     * Constructor
     * @param string|null $basePath
     * @param string|null $parentSlug
     */
    public function __construct($basePath = null, $parentSlug = null)
    {
        if (null !== $basePath) {
            $this->loadComposerAutoloader($basePath);
            $this->basePath = $basePath;
        }
        if (null !== $parentSlug) {
            $this->parentSlug = $parentSlug;
        }
    }

    /**
     * Load Composer autoloader from another path
     *
     * Please note Composer is designed to support loading multiple autoloaders, however, if the same package appears in
     * both Composer autoloaders with different versions this will likely cause issues
     *
     * @param $path
     * @return void
     */
    public function loadComposerAutoloader($path)
    {
        $autoload = rtrim($path, DIRECTORY_SEPARATOR) . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new \InvalidArgumentException(sprintf('Composer autoloader not found at %s', $autoload));
        }
        require_once $autoload;
    }

    /**
     * Exclude Composer package from collectData()
     * @param $slug
     * @return void
     */
    public function exclude($slug)
    {
        $this->exclude[] = $slug;
    }

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $this->data = new VersionCollection();
        $this->getDependencies();
        return $this->data;
    }

    /**
     * Get version of a package
     * @param $package
     * @return string|null
     */
    public function getPackageVersion($package)
    {
        return InstalledVersions::getVersion($package);
    }

    /**
     * Get all installed dependencies from Composer
     *
     * @param bool $includeAll
     * @return array
     */
    protected function getDependencies($includeAll = true)
    {
        // This returns all dependencies, including dependencies of dependencies.
        // Setting $includeAll to false strips out all dependencies that are not
        // mentioned in the composer.json file.

        $installedDependencies = InstalledVersions::getInstalledPackages();
        $dependenciesFromComposerJson = $this->getDependenciesFromJson();

        $data = [];
        foreach ($installedDependencies as $name) {
            if (in_array($name, $this->exclude)) {
                continue;
            }
            if (!$includeAll) {
                // We'll check if the installed package was mentioned in composer.json
                $include = false;
                foreach ($dependenciesFromComposerJson as $jsonDependency) {
                    if ($jsonDependency['slug'] === $name) {
                        $include = true;
                    }
                }

                // If it wasn't, skip it.
                if (!$include) {
                    continue;
                }
            }

            $this->data->add($name, InstalledVersions::getVersion($name), $this->parentSlug);
        }
    }

    /**
     * Lookup dependencies from composer.json
     * @return array
     */
    protected function getDependenciesFromJson()
    {
        if (!$composer = file_get_contents($this->basePath . DIRECTORY_SEPARATOR . 'composer.json')) {
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
