<?php

namespace Studio24\Agent;

/**
 * Simple class to manage CLI input and output
 */
class Cli
{
    const ARGUMENT_SPACING = 23;
    const RED   = "\033[31m";
    const GREEN = "\033[32m";
    const CLEAR = "\033[0m";

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
            self::title($title);
        }
    }

    public static function title($title)
    {
        echo self::GREEN;
        echo $title . PHP_EOL;
        echo str_pad('', strlen($title), '=') . PHP_EOL . PHP_EOL;
        echo self::CLEAR;
    }

    public static function info($message)
    {
        echo $message . PHP_EOL;
    }


    public static function error($message)
    {
        echo self::RED;
        echo $message . PHP_EOL;
        echo self::CLEAR;
    }

    /**
     * Help command
     * @param string $description Help description
     * @param string $usage Usage text
     * @param array $arguments Array of arguments and description
     * @param bool $exit Whether to exit after displaying help text
     */
    public function help($description, $usage, $arguments)
    {
        $usage = '  ' . $usage;
        $argumentsText = '';
        foreach ($arguments as $key => $value) {
            $argumentsText .= '  ' . $key . str_pad('', self::ARGUMENT_SPACING - strlen($key), ' ') . $value . PHP_EOL;
        }
        $green = self::GREEN;
        $clear = self::CLEAR;
        echo <<<EOD
$description

{$green}Usage:{$clear}
$usage

{$green}Arguments:{$clear}
$argumentsText

EOD;

    }

    /**
     * Setup command
     */
    public function setup()
    {
        $from = __DIR__ . '/../config/agent-config.php';
        $to = getcwd() . '/agent-config.php';

        if (!file_exists($from)) {
            self::error("Example config file not found at $from");
            exit(1);
        }
        if (file_exists($to)) {
            self::error("Config file already exists at $to");
            exit(1);
        }
        if (!copy($from, $to)) {
            self::error("Cannot copy example config file to $to");
            exit(1);
        }
        $green = self::GREEN;
        $clear = self::CLEAR;
        echo <<<EOD
{$green}Example config file copied to $to{$clear}

EOD;

        $from = __DIR__ . '/../config/.env';
        $to = getcwd() . '/.env';

        if (!file_exists($from)) {
            self::error("Example .env file not found at $from");
            exit(1);
        }
        if (file_exists($to)) {
            self::error(".env file already exists at $to");
            exit(1);
        }
        if (!copy($from, $to)) {
            self::error("Cannot copy example .env file to $to");
            exit(1);
        }
        echo <<<EOD
{$green}Example .env file copied to $to{$clear}

EOD;
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
