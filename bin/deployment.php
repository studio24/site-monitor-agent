<?php

ini_set('display_errors', '1');
error_reporting(E_ALL ^ E_DEPRECATED);

require 'vendor/autoload.php';

use Studio24\Agent\Agent;
use Studio24\Agent\Cli;
use Studio24\Agent\Config;
use Studio24\Agent\HttpClient;

$cli = new Cli($argc, $argv, 'Site monitor agent deployment');

// Run help command
if (isset($argv[1]) && in_array($argv[1], ['--help', '-help', '-h', '-?'])) {
    $cli->help('Sends a deployment to a central server. ', 'php agent.php [<branch>, <author>]', [
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

// Create deployment

$branch = $action = $cli->getArgument(1);
$author = $action = $cli->getArgument(2);
$date = $action = $cli->getArgument(3) ? $action = $cli->getArgument(3) : date('Y-m-d H:i:s');

$data = [
    'url' => $config->url,
    'branch' => $branch,
    'author' => $author,
    'date' => $date,
];

// Send request
if ($branch && $author && $date) {

    echo sprintf("Sending data to API endpoint %s", $config->apiBaseUrl) . PHP_EOL;
    echo json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;



    $httpClient = new HttpClient($config->apiBaseUrl, $config->apiToken);

    /*

    // Testing (move this to a unit test)
    $mock = new \GuzzleHttp\Handler\MockHandler([
        new \GuzzleHttp\Psr7\Response(200, [], '{"message": "OK"}'),
    ]);
    $handlerStack = \GuzzleHttp\HandlerStack::create($mock);
    $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);
    $httpClient->setClient($client);

    */

    $response = $httpClient->sendDeployment($data);

    echo 'Response: ' . $response->getBody() . PHP_EOL;
} else {
    echo 'Missing at least one of required parameters: branch, author, date' . PHP_EOL;
    echo json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
    exit(1);
}

// Success!
exit(0);
