<?php

namespace Studio24\Agent\Command;

class HelpCommand implements CommandInterface
{
    /**
     * Run the command
     */
    public function run()
    {
        $cli->help('Collects data from a website or web application and sends this to a central server. Run without any arguments to collect data and output to terminal (dry run mode: no data is sent).', 'php agent.php [<send>]', [
            'send' => 'Send data to API endpoint, if this argument is not set then no data is sent',
            'setup' => 'Copy example config file to project',
            '-v' => 'Verbose mode',
            '--help' => 'This help text',
        ]);
        exit(0);
    }
}