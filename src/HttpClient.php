<?php

namespace Studio24\Agent;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Studio24\Agent\Exception\FailedHttpRequestException;
use Studio24\Agent\Traits\TypeTrait;

class HttpClient
{
    use TypeTrait;

    const API_SEND_DATA_URL = '/api/v1/update';
    const API_SEND_DEPLOYMENT_URL = '/api/v1/deployment';

    /** @var Client */
    private $client;

    /**
     * Constructor
     * @param string $endpointUrl
     * @param string $authToken
     */
    public function __construct($endpointUrl, $authToken)
    {
        /**
         * Set default client
         * @see https://docs.guzzlephp.org/en/6.5/request-options.html
         * @see https://docs.guzzlephp.org/en/latest/request-options.html
         */
        $this->setClient(new Client([
            'verify' => false, // Required for DDEV SSL certs
            'base_uri' => $endpointUrl,
            'headers' => [
                'Authorization' => "Bearer {$authToken}",
                'Accept' => 'application/json',
                'User-Agent' => Version::getUserAgent(),
            ]
        ]));
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient($client)
    {
        $this->throwIfNotInstanceOf('\GuzzleHttp\ClientInterface', 'client', $client);
        $this->client = $client;
    }

    /**
     * Send array of data to site monitoring tool
     *
     * Expecting JSON array:
     * - name
     * - url
     * - repo_url
     * - versions (array)
     *   - slug
     *   - version
     *   - parent
     *
     * @param array $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendData($data)
    {
        $this->throwIfNotArray('data', $data);
        $response = $this->client->request('POST', self::API_SEND_DATA_URL, [
            'json' => $data
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send sendData HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }

        return $response;
    }

    /**
     * Send array of data to site monitoring tool for deployment
     *
     *  Expecting JSON array:
     *  - author
     *  - date
     *  - branch
     *
     * @param array $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendDeployment($data)
    {
        $this->throwIfNotArray('data', $data);
        $response = $this->client->request('POST', self::API_SEND_DEPLOYMENT_URL, [
            'json' => $data
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send sendData HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }

        return $response;
    }
}
