<?php

namespace Studio24\Agent\Traits;

use Studio24\Agent\Exception\CommandException;

trait ExecTrait
{
    /**
     * Execute a server command and return the output
     *
     * @param $command
     * @param $arguments
     * @param $path
     * @return string|null The entire output of the command
     */
    public function exec($command, $arguments = null, $path = null)
    {
        // Set path to run command from
        $defaultLocations = [
            '/usr/sbin',
            '/usr/bin',
            '/bin',
        ];
        if ($path !== null) {
            $locations = [$path];
        } else {
            $locations = $defaultLocations;
        }

        // Run the command if it exists in one of the $defaultLocations
        foreach ($locations as $location) {
            if (file_exists(sprintf('%s/%s', $location, $command))) {
                $command = sprintf('%s/%s %s', $location, $command, $arguments);
                exec($command, $output, $resultCode);
                if ($resultCode !== 0) {
                    throw new CommandException(sprintf("Command %s failed, returning error code: %d", $command, $resultCode));
                }
                return implode(PHP_EOL, $output);
            }
        }

        throw new CommandException(sprintf("Command %s cannot be found in %s", $command, implode(', ', $defaultLocations)));
    }
}
