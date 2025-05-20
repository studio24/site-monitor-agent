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
    $cli->help('Collects data from a website or web application and sends this to a central server. Run without any arguments to collect data and output to terminal (dry run mode: no data is sent).',
        'php agent.php [<send>]', [
        'send' => 'Send data to API endpoint, if this argument is not set then no data is sent',
        'setup' => 'Copy example config file to project',
        'ping' => 'Send ping request to test you can communicate with server OK',
        'config' => 'Output config settings used by site monitor agent',
        '-v' => 'Verbose mode',
        '--help' => 'This help text',
    ]);
    exit(Cli::SUCCESS);
}

// Verbose mode?
$verbose = false;
if (isset($argv[1]) && in_array('-v', $argv)) {
    $verbose = true;
    Cli::info('Verbose mode');
}

// Action
$ping = false;
$outputConfig = false;
$collect = false;
$send = false;
switch ($cli->getArgument(1)) {
    case 'ping':
        // Ping
        $ping = true;
        break;
    case 'setup':
        // Run setup command and exit
        $cli->setup();
        exit(0);
        break;
    case 'config':
        // Output config
        $outputConfig = true;
        break;
    case 'send':
        // Collect and send data
        $collect = true;
        $send = true;
        break;
    default:
        // Just collect data and output
        $collect = true;
}

// Run collect data command
$config = new Config();
$config->setVerbose($verbose);
$config->validate();

if ($outputConfig) {
    echo sprintf("Config settings loaded from %s", $config->getLoadedConfigFile()) . PHP_EOL;
    echo $config->getJson();
    exit(Cli::SUCCESS);
}

// Setup agent
$agent = new Agent();
$agent->setVerbose($verbose);
$agent->setCollectors($config->collectors);
$agent->setSiteId($config->siteId);
$agent->setEnvironment($config->environment);
$agent->setGitRepoUrl($config->gitRepoUrl);
$agent->setUrl($config->url);
$agent->setAccount($config->account);
$agent->setServerName($config->serverName);

$httpClient = new HttpClient($config->apiBaseUrl, $config->apiToken);
$httpClient->setVerbose($verbose);

// Ping
if ($ping) {
    echo 'Ping...' . PHP_EOL;
    $response = $httpClient->ping();
    echo 'Response: ' . $response->getBody() . PHP_EOL;
    exit(Cli::SUCCESS);
}

// Collect data
if ($collect) {
    echo sprintf("Collecting data for site ID %s", $config->siteId) . PHP_EOL;
    $agent->collectData();
    echo $agent->toJson(true) . PHP_EOL;
}

// Send data
if ($send) {
    echo sprintf("Sending data to API endpoint %s", $config->apiBaseUrl) . PHP_EOL;
    $response = $httpClient->sendData($agent);
    echo 'Response: ' . $response->getBody() . PHP_EOL;
} else {
    echo "Dry run mode" . PHP_EOL;
}

// Success!
exit(Cli::SUCCESS);
