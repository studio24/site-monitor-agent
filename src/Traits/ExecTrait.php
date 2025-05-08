<?php

namespace Studio24\Agent\Traits;

use Studio24\Agent\Exception\CommandException;

trait ExecTrait
{
    /**
     * Execute a server command and return the output
     *
     * @param $command Command
     * @param $arguments Command arguments
     * @param $path Path to command
     * @param $cd Change directory before running command
     * @return string|null The entire output of the command
     */
    public function exec($command, $arguments = null, $path = null, $cd = null)
    {
        // Set path to run command from
        $defaultLocations = [
            '/usr/sbin',
            '/usr/bin',
            '/bin',
            '/usr/local/bin/',
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

                // CD
                if ($cd !== null) {
                    $previousPath = getcwd();
                    chdir($cd);
                }

                // Run command
                exec(escapeshellcmd($command), $output, $resultCode);

                // Switch CD back
                if ($cd !== null) {
                    chdir($previousPath);
                }

                // Test return code
                if ($resultCode !== 0) {
                    throw new CommandException(sprintf("Command %s failed, returning error code: %d", $command, $resultCode));
                }

                // Return all lines of output as a string
                return implode(PHP_EOL, $output);
            }
        }

        throw new CommandException(sprintf("Command %s cannot be found in %s", $command, implode(', ', $defaultLocations)));
    }
}
