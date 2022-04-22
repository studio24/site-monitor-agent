<?php

namespace Studio24\Agent\Collector;

use Studio24\Agent\Cli;

class WordPress implements CollectorInterface, VerboseInterface, ApplicationInterface
{
    use VerboseTrait, ApplicationTrait;

    /** @var string[] */
    private $wordPressBasePaths = [
        'web',
        'htdocs',
        'web/wordpress',
        'htdocs/wordpress',
    ];

    /** @var string[]  */
    private $wordPressPluginPaths = [
        'web/wp-content/plugins',
        'web/content/plugins',
        'htdocs/content/plugins',
    ];

    /** @var string */
    private $wordPressBasePath;

    /** @var string */
    private $wordPressVersion;

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
        // Detect paths
        if ($this->wordPressBasePath !== null) {
            if (!$this->detectWordPress($this->wordPressBasePath)) {
                Cli::error("WordPress installation not found at " . $this->wordPressBasePath);
                $this->wordPressBasePath = null;
            }
        }
        if (!$this->foundWordPress()) {
            foreach ($this->wordPressBasePaths as $path) {
                if ($this->detectWordPress(getcwd() . DIRECTORY_SEPARATOR . ltrim($path, '/'))) {
                    break;
                }
            }
        }
        if (!$this->foundWordPress()) {
            Cli::error("WordPress installation not found");
            // @todo report error to API?
        }

//        $this->bootstapWordPress();
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

        $includesPath = $path . DIRECTORY_SEPARATOR . 'wp-includes' . DIRECTORY_SEPARATOR;
        if (is_dir($includesPath) && file_exists($includesPath . 'version.php')) {
            require $includesPath . 'version.php';
            if (isset($wp_version)) {
                $this->wordPressVersion = $wp_version;
                $this->wordPressBasePath = $path;

                if ($this->isVerbose()) {
                    Cli::info("WordPress installation found at $path");
                }

//                $this->bootstrapWordPress();
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
        return (!empty($this->wordPressBasePath));
    }

    /**
     * Include initial WordPress files so we can access WP data
     */
    public function bootstrapWordPress()
    {
        if ($this->foundWordPress()) {
            require $this->wordPressBasePath . DIRECTORY_SEPARATOR . 'wp-load.php';
        }
    }

    /**
     * Return collector name
     * @return string
     */
    public function getName()
    {
        return 'WordPress';
    }

    /**
     * @return string
     */
    public function getWordPressVersion()
    {
        return $this->wordPressVersion;
    }

    public function getPluginVersions()
    {
//        $plugins = \get_plugins();var_dump($plugins);

        if (defined('WP_PLUGIN_DIR')) {
            $pluginDirectory = WP_PLUGIN_DIR;
        }

        //
        return [];
    }

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        $this->findWordPress();

        return [
            'version' => $this->getWordPressVersion(),
            'plugins' => $this->getPluginVersions()
        ];
    }
}
