<?php

namespace Studio24\Agent;

use Studio24\Agent\Collector\VerboseTrait;
use Studio24\Agent\Exception\InvalidConfigException;

class Config
{
    use VerboseTrait;

    const CONFIG_FILENAME = 'agent-config.php';

    /** @var array */
    private $expected = [
        'string' => [
            'apiBaseUrl', 'apiToken', 'siteId', 'environment', 'gitRepoUrl'
        ],
        'array' => [
            'collectors'
        ]
    ];

    /** @var string[] */
    private $paths = [
        './',
        '../',
        '../config/',
        '../../../../',
    ];

    /** @var string */
    private $basePath;

    /** @var array */
    private $config = null;

    /**
     * @param array $paths Any other paths to load the config file from
     */
    public function __construct($paths = [])
    {
        if (!empty($paths)) {
            $this->paths = array_merge($this->paths, $paths);
        }
    }

    /**
     * Test whether the config has the required settings
     * @throws InvalidConfigException
     */
    public function validate()
    {
        $this->getConfig();
        $errors = [];
        foreach ($this->expected['string'] as $expected) {
            if (!$this->has($expected)) {
                $errors[] = sprintf('%s is missing', $expected);
            }
            if (!is_string($this->get($expected))) {
                $errors[] = sprintf('%s must be a string', $expected);
            }
        }
        foreach ($this->expected['array'] as $expected) {
            if (!$this->has($expected)) {
                $errors[] = sprintf('%s is missing', $expected);
            }
            if (!is_array($this->get($expected))) {
                $errors[] = sprintf('%s must be an array', $expected);
            }
        }

        if (!empty($errors)) {
            throw new InvalidConfigException("Config file is not valid: " . implode(', ', $errors));
        }
    }

    /**
     * Return array of config
     * @return array
     */
    public function getConfig()
    {
        if (is_array($this->config)) {
            return $this->config;
        }

        $tried = [];
        foreach ($this->paths as $path) {
            $filepath = getcwd() . '/' . trim($path, '/') . '/' . self::CONFIG_FILENAME;
            $filepath = realpath($filepath);
            if ($filepath !== false && file_exists($filepath)) {
                $this->config = include($filepath);
                if (!is_array($this->config)) {
                    throw new InvalidConfigException('Config file must only return an array');
                }

                $this->config = $this->parseTokens(dirname($filepath), $this->config);

                if ($this->isVerbose()) {
                    Cli::info("Config file loaded from $filepath");
                }
            }
            $tried[] = $filepath;
        }
        if ($this->config === null) {
            throw new InvalidConfigException('Cannot find config file in paths: ' . implode(', ', $tried));
        }
        if (empty($this->config)) {
            throw new InvalidConfigException('Cannot load config file or config array is empty');
        }
    }

    /**
     * Parse any tokens in a config data array and return the parsed data array
     *
     * @param string $path Path to load .env file from
     * @param array $data Data array
     * @return array Parsed data array
     */
    public function parseTokens($path, $data)
    {
        // Load .env file
        $envPath = $path . DIRECTORY_SEPARATOR . '.env';
        $envFile = false;
        if (file_exists($envPath)) {
            $envFile = file_get_contents($envPath);
            if ($envFile === false) {
                Cli::error('Cannot load .env file from ' . $envPath);
            }
        } else {
            Cli::error('Not exists .env file from ' . $envPath);
        }

        foreach ($data as $name => $value) {
            
            // Skip the collectors array.
            if (is_array($value)) {
                continue;
            }

            if (preg_match('/^%(.+)%$/', $value, $m)) {
                $token = $m[1];

                // Check environment variable
                $env = getenv($token);
                if ($env !== false) {
                    $data[$name] = $env;
                    continue;
                }

                // Check .env file
                if ($envFile !== false) {
                    if (preg_match('/^' . $token . '=(.+)$/m', $envFile, $m)) {
                        $data[$name] = trim($m[1]);
                    }
                }
            }
        }
        return $data;
    }


    /**
     * @param $name
     * @return bool
     * @throws InvalidConfigException
     */
    public function has($name)
    {
        $config = $this->getConfig();
        return isset($config[$name]);
    }

    /**
     * Return config value, or null if not set
     * @param $name
     * @return mixed|null
     * @throws InvalidConfigException
     */
    public function get($name)
    {
        $config = $this->getConfig();

        if ($this->has($name)) {
            return $config[$name];
        }

        return null;
    }

    public function __get($name)
    {
        return $this->get($name);
    }
}
