<?php

ini_set('display_errors', '1');
error_reporting(E_ALL ^ E_DEPRECATED);

require 'vendor/autoload.php';

use Studio24\Agent\Agent;
use Studio24\Agent\Config;
use Studio24\Agent\HttpClient;

// Get config
$config = new Config();
$config->validate();

echo sprintf("Collecting data for site ID %s", $config->siteId) . PHP_EOL;

// Get data
$agent = new Agent($config->collectors);
$data = $agent->collectData();

// Send request
$httpClient = new HttpClient($config->apiBaseUrl, $config->apiToken);

// Testing (move this to a unit test)
$mock = new \GuzzleHttp\Handler\MockHandler([
    new \GuzzleHttp\Psr7\Response(200, [], '{"message": "OK"}'),
]);
$handlerStack = \GuzzleHttp\HandlerStack::create($mock);
$client = new \GuzzleHttp\Client(['handler' => $handlerStack]);
$httpClient->setClient($client);

$httpClient->sendData($data);

// Success!
echo "Data sent successfully" . PHP_EOL;
exit(0);
