<?php

namespace Studio24\Agent;

use Studio24\Agent\Exception\InvalidConfigException;

class Config
{
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
        '../../../../',
        '../',
        '../config/',
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
            $filepath = __DIR__ . '/' . trim($path, '/') . '/' . self::CONFIG_FILENAME;
            if (file_exists($filepath)) {
                $this->config = include($filepath);
                if (!is_array($this->config)) {
                    throw new InvalidConfigException('Config file must only return an array');
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
