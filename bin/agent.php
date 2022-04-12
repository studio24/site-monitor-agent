<?php

ini_set('display_errors', '1');
error_reporting(E_ALL ^ E_DEPRECATED);

require 'vendor/autoload.php';

use Studio24\Agent\Agent;
use Studio24\Agent\Cli;
use Studio24\Agent\Config;
use Studio24\Agent\HttpClient;

$cli = new Cli($argc, $argv, 'Site monitor agent');
$cli->help('Collects data from a website or web application and sends this to a central server.', 'php agent.php [<send>]', [
    'send' => 'Send data to API endpoint, if this argument is not set then no data is sent'
]);

$action = $cli->getArgument(1);
switch ($action) {
    case 'send':
        $send = true;
        break;
    default:
        $send = false;
}

$config = new Config();
$config->validate();

echo sprintf("Collecting data for site ID %s", $config->siteId) . PHP_EOL;
if ($send) {
    echo sprintf("Sending data to API endpoint %s", $config->apiBaseUrl) . PHP_EOL;
} else {
    echo "Dry run mode" . PHP_EOL;
}

// Setup agent
$agent = new Agent($config->collectors);
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
