<?php

namespace Studio24\Agent;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Studio24\Agent\Exception\FailedHttpRequestException;

class HttpClient
{
    const API_SITE_DATA_URL = '/site/data';
    const API_ERROR_URL = '/error';

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
        $response = $this->client->request('POST', self::API_SITE_DATA_URL, [
            'json' => $data
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send sendData HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }
    }

    /**
     * Send error message and any relevant data to the site monitoring tool
     * @param string $message
     * @param array $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reportError($message, $data)
    {
        $response = $this->client->request('POST', self::API_ERROR_URL, [
            'json' => [
                'message' => $message,
                'data' => $data
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send reportError HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }
    }

}
