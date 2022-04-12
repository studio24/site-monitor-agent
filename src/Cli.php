<?php

namespace Studio24\Agent;

/**
 * Simple class to manage CLI input and output
 */
class Cli
{
    public $red   = "\033[31m";
    public $green = "\033[32m";
    public $clear = "\033[0m";

    /** @var int int */
    public $argc = 0;

    /** @var array */
    public $argv = [];

    /**
     * Constructor
     * @param int $argc https://www.php.net/manual/en/reserved.variables.argc.php
     * @param array $argv https://www.php.net/manual/en/reserved.variables.argv.php
     * @param null|string $title CLI command title, outputs via title()
     */
    public function __construct($argc, $argv, $title = null)
    {
        if (!is_int($argc)) {
            throw new \InvalidArgumentException('$argc must have integer values');
        }
        if (!is_array($argv)) {
            throw new \InvalidArgumentException('$argv must be an array');
        }
        $this->argc = $argc;
        $this->argv = $argv;

        if (!empty($title)) {
            $this->title($title);
        }
    }

    public function title($title)
    {
        echo $this->green;
        echo $title . PHP_EOL;
        echo str_pad('', strlen($title), '=') . PHP_EOL . PHP_EOL;
        echo $this->clear;
    }

    public function error($message)
    {
        echo $this->red;
        echo $message . PHP_EOL;
        echo $this->clear;
    }

    public function help($description, $usage, $arguments)
    {
        if (!isset($this->argv[1]) || !in_array($this->argv[1], array('--help', '-help', '-h', '-?'))) {
            return;
        }

        $usage = '  ' . $usage;
        $argumentsText = '';
        foreach ($arguments as $key => $value) {
            $argumentsText = '  ' . $key . str_pad('', 23 - strlen($key), ' ') . $value;
        }
        echo <<<EOD
$description

{$this->green}Usage:{$this->clear}
$usage

{$this->green}Arguments:{$this->clear}
$argumentsText

EOD;

        exit(0);
    }

    /**
     * Return argument, or return default value if not set
     * @param int $argumentNumber 1 for first argument, 2 for second argument, etc
     * @param mixed $default Default value to return if argument not found
     * @return mixed
     */
    public function getArgument($argumentNumber, $default = null)
    {
        if (($this->argc >= ($argumentNumber + 1) && isset($this->argv[$argumentNumber]))) {
            return $this->argv[$argumentNumber];
        }
        return $default;
    }
}
