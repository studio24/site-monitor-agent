<?php

namespace Studio24\Agent;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Studio24\Agent\Exception\FailedHttpRequestException;

class HttpClient
{
    const API_ENDPOINT_URL = '/site/data';
    const USER_AGENT = 'studio24/agent (+https://github.com/studio24/site-monitor-agent/)';

    /** @var Client */
    private $client;

    /**
     * Constructor
     * @param string $endpointUrl
     * @param string $authToken
     */
    public function __construct($endpointUrl, $authToken)
    {
        // @see https://docs.guzzlephp.org/en/6.5/request-options.html
        $this->client = new Client([
            'base_uri' => $endpointUrl,
            ['headers' =>
                ['Authorization' => "Bearer {$authToken}"],
                ['User-Agent' => self::USER_AGENT],
            ]
        ]);
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Send array of data to site monitoring tool
     * @param array $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendData($data)
    {
        $response = $this->client->request('POST', self::API_ENDPOINT_URL, [
            'json' => $data
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }
    }

}