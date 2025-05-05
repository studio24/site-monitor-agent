<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Cli;
use Studio24\Agent\Interfaces\ApplicationInterface;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Interfaces\VerboseInterface;
use Studio24\Agent\Model\VersionCollection;
use Studio24\Agent\Traits\ApplicationTrait;
use Studio24\Agent\Traits\VerboseTrait;

class WordPress implements CollectorInterface, VerboseInterface, ApplicationInterface
{
    use VerboseTrait;
    use ApplicationTrait;

    /** @var VersionCollection */
    private $data;

    /** @var string[] */
    private $defaultBasePaths = [
        'web',
        'htdocs',
        'web/wordpress',
        'htdocs/wordpress'
    ];

    /** @var string[]  */
    private $defaultPluginPaths = [
        'wp-content/plugins',
        'content/plugins',
    ];

    /** @var ?string */
    private $wordPressBasePath = null;

    /** @var ?string */
    private $wordPressVersion = null;

    /**
     * Constructor
     * @param null $wordPressBasePath
     */
    public function __construct($wordPressBasePath = null)
    {
        if ($wordPressBasePath !== null) {
            $this->wordPressBasePath = $wordPressBasePath;
        }
    }

    /**
     * Find WordPress installation
     */
    public function findWordPress()
    {
        $attempted = [];

        // Try the passed base path
        if (null !== $this->wordPressBasePath) {
            $attempted[] = $this->wordPressBasePath;
            if (!$this->detectWordPress($this->wordPressBasePath)) {
                Cli::error("WordPress installation not found at " . $this->wordPressBasePath);
                $this->wordPressBasePath = null;
            }
        }

        // Try the default base paths
        if (!$this->foundWordPress()) {
            foreach ($this->defaultBasePaths as $path) {
                $attempted[] = $path;
                if ($this->detectWordPress(getcwd() . DIRECTORY_SEPARATOR . ltrim($path, '/'))) {
                    break;
                }
            }
        }

        if ($this->foundWordPress()) {
            $this->data->add('wordpress', $this->wordPressVersion);
        } else {
            $message = sprintf('Cannot find WordPress installation in these paths: %s', implode(', ', $attempted));
            Cli::error($message);
            $this->data->add('wordpress', null, null, $message);
            return;
        }
    }

    /**
     * Detect a WordPress installation and get version
     * @param string $path Path to test for WordPress installation
     * @return bool
     */
    public function detectWordPress($path)
    {
        if (empty($path)) {
            return false;
        }

        Cli::info(sprintf('Looking for WordPress in %s', $path));
        $includesPath = $path . DIRECTORY_SEPARATOR . 'wp-includes' . DIRECTORY_SEPARATOR;
        if (is_dir($includesPath) && file_exists($includesPath . 'version.php')) {
            /** @link https://github.com/WordPress/WordPress/blob/master/wp-includes/version.php */
            require $includesPath . 'version.php';
            if (isset($wp_version)) {
                $this->wordPressVersion = $wp_version;
                $this->wordPressBasePath = $path;

                Cli::info("WordPress installation found at $path");
                return true;
            }
        }
        return false;
    }

    /**
     * Whether we have found a WordPress installation or not
     * @return bool
     */
    public function foundWordPress()
    {
        return (null !== $this->wordPressVersion);
    }

    /**
     * @return string
     */
    public function getWordPressVersion()
    {
        return $this->wordPressVersion;
    }

   /**
     * Find and parse plugin names and versions
     * @return array
     */
    public function findPlugins()
    {
        // Find plugins in the WordPress installation
        foreach ($this->defaultPluginPaths as $pluginDir) {
            if (!file_exists($this->wordPressBasePath . DIRECTORY_SEPARATOR . $pluginDir)) {
                 continue;
            }

            /**
             * Find the root plugin PHP file which contains header fields
             * @see https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
             */
            $pluginRootFiles = glob($this->wordPressBasePath . DIRECTORY_SEPARATOR . $pluginDir . '/*/*.php');
            if (is_array($pluginRootFiles)) {
                foreach ($pluginRootFiles as $file) {
                    // Test each root PHP file
                    $contents = file_get_contents($file);

                    if (preg_match('/Plugin Name: *(.+)/', $contents, $m) && !empty($m[1])) {
                        $name = trim($m[1]);

                        // Detect plugin slug from plugin foldername
                        $folders = explode('/', dirname($file));
                        $slug = end($folders);

                        // Detect plugin version
                        preg_match('/Version: *(.+)/', $contents, $m);
                        if (!empty($m[1])) {
                            $version = trim($m[1]);
                            $this->data->add($slug, $version, 'wordpress');
                        } else {
                            $this->data->add($slug, null, 'wordpress', sprintf('Cannot determine version from plugin file %s', $file));
                        }
                    }
                }
            }
        }
    }

    /**
     * Collect data
     * @return VersionCollection
     */
    public function collectData()
    {
        $this->data = new VersionCollection();
        $this->findWordPress();
        $this->findPlugins();
        return $this->data;
    }
}
