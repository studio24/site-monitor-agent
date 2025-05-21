<?php

namespace Studio24\Agent\Collector;

use Composer\InstalledVersions;
use Studio24\Agent\Exec;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Model\VersionCollection;

class Composer implements CollectorInterface
{
    /** @var VersionCollection */
    private $data;

    private $basePath = './';
    private $exclude = [];
    private $composerTree = [];

    /**
     * Constructor
     * @param string|null $basePath Base path to composer.json and vendor folder
     */
    public function __construct($basePath = null)
    {
        if (null !== $basePath) {
            $this->loadComposerAutoloader($basePath);
            $this->basePath = $basePath;
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
    protected function getDependencies()
    {
        $data = [];
        foreach (InstalledVersions::getInstalledPackages() as $name) {
            if (in_array($name, $this->exclude)) {
                continue;
            }
            if ($this->isPlatformDependency($name)) {
                continue;
            }
            $version = InstalledVersions::getVersion($name);
            if (null === $version) {
                continue;
            }
            $this->data->add($name, $version, $this->getParent($name));
        }
    }

    /**
     * Is the Composer package a platform dependency?
     *
     * @param $name
     * @return bool
     */
    public function isPlatformDependency($name)
    {
        $name = strtolower($name);
        if (in_array($name, ['php', 'hhvm', 'php-64bit'])) {
            return true;
        }
        if (preg_match('/^(ext|lib)\-.+$/', $name)) {
            return true;
        }

        return false;
    }

    /**
     * Return Composer packages in a dependency tree
     *
     * @return void
     * @throws \Studio24\Agent\Exception\CommandException
     */
    public function getComposerTree()
    {
        $json = Exec::exec('composer', 'show --tree --format=json', null, $this->basePath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(sprintf('Error decoding JSON from composer show --tree: %s', json_last_error_msg()));
        }
        if (!isset($data["installed"]) || !is_array($data["installed"])) {
            throw new \RuntimeException('installed data not found in composer show --tree JSON');
        }

        $this->processTreeDependencies($data["installed"]);
        return $this->composerTree;
    }

    /**
     * Process dependencies of a package tree and organizes them by parent-child relationships.
     *
     * @param array $requires List of dependencies to process.
     * @param string|null $parent Name of the parent package, or null for the root.
     * @return void
     */
    protected function processTreeDependencies($requires, $parent = null)
    {
        foreach ($requires as $item) {
            if (!isset($item["name"])) {
                continue;
            }
            $name = $item["name"];
            if ($this->isPlatformDependency($name)) {
                continue;
            }

            // Store package name with parent/s
            if (!empty($this->composerTree[$name])) {
                if (strpos($this->composerTree[$name], $parent) !== false) {
                    continue;
                }
                $this->composerTree[$name] .= ',' . $parent;
            } else {
                $this->composerTree[$name] = $parent;
            }

            // Process children
            if (isset($item["requires"]) && is_array($item["requires"])) {
                $this->processTreeDependencies($item["requires"], $name);
            }
        }
    }

    /**
     * Retrieve the parent package/s for a given Composer package
     *
     * @param string $name
     * @return string|null List of parent packages, separated by comma
     */
    public function getParent($name)
    {
        // Lazy load composer tree
        if ($this->composerTree === []) {
            $this->getComposerTree();
        }

        if (isset($this->composerTree[$name])) {
            return $this->composerTree[$name];
        }
        return null;
    }
}
