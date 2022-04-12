<?php

namespace Studio24\Agent;

use Studio24\Agent\Exception\CommandException;

/**
 * Simple class to manage running exec
 *
 * Usage:
 * $runner = new CommandRunner();
 * $runner->exec("httpd -v");
 * $output = $runner->getOutput();
 */
class CommandRunner
{
    /**
     * Array of the command output, one line per array row
     *
     * @var array
     */
    protected $output = [];

    /**
     * Run a command through exec
     *
     * Throws an exception on error
     *
     * @param string $command
     * @return string The last line from the result of the command, get all output via $this->getOutput()
     * @throws CommandException
     */
    public function exec($command)
    {
        $this->output = [];
        $command = escapeshellcmd($command);
        $results = exec($command, $this->output, $return);

        if ($return != 0) {
            throw new CommandException("Command returned error code: " . $return);
        }

        return $results;
    }

    /**
     * Return full command output as an array
     *
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }
}
