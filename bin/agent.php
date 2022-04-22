<?php

ini_set('display_errors', '1');
error_reporting(E_ALL ^ E_DEPRECATED);

require 'vendor/autoload.php';

use Studio24\Agent\Agent;
use Studio24\Agent\Cli;
use Studio24\Agent\Config;
use Studio24\Agent\HttpClient;

$cli = new Cli($argc, $argv, 'Site monitor agent');

// Run help command
if (isset($argv[1]) && in_array($argv[1], ['--help', '-help', '-h', '-?'])) {
    $cli->help('Collects data from a website or web application and sends this to a central server. Run without any arguments to collect data and output to terminal (dry run mode: no data is sent).', 'php agent.php [<send>]', [
        'send' => 'Send data to API endpoint, if this argument is not set then no data is sent',
        'setup' => 'Copy example config file to project',
        '-v' => 'Verbose mode',
        '--help' => 'This help text',
    ]);
    exit(0);
}

// Verbose mode?
$verbose = false;
if (isset($argv[1]) && in_array('-v', $argv)) {
    $verbose = true;
    Cli::info('Verbose mode');
}

$action = $cli->getArgument(1);
switch ($action) {
    case 'setup':
        // Run setup command
        $cli->setup();
        exit(0);
        break;
    case 'send':
        $send = true;
        break;
    default:
        $send = false;
}

// Run collect data command
$config = new Config();
$config->setVerbose($verbose);
$config->validate();

echo sprintf("Collecting data for site ID %s", $config->siteId) . PHP_EOL;
if ($send) {
    echo sprintf("Sending data to API endpoint %s", $config->apiBaseUrl) . PHP_EOL;
} else {
    echo "Dry run mode" . PHP_EOL;
}

// Setup agent
$agent = new Agent();
$agent->setVerbose($verbose);
$agent->setCollectors($config->collectors);
$agent->setSiteId($config->siteId);
$agent->setEnvironment($config->environment);
$agent->setGitRepoUrl($config->gitRepoUrl);

// Collect data
$data = $agent->collectData();
echo json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;

// Send request
if ($send) {

    $httpClient = new HttpClient($config->apiBaseUrl, $config->apiToken);

    // Testing (move this to a unit test)
    $mock = new \GuzzleHttp\Handler\MockHandler([
        new \GuzzleHttp\Psr7\Response(200, [], '{"message": "OK"}'),
    ]);
    $handlerStack = \GuzzleHttp\HandlerStack::create($mock);
    $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);
    $httpClient->setClient($client);

    $httpClient->sendData($data);

    echo "Data sent: " . PHP_EOL;
}

// Success!
exit(0);
