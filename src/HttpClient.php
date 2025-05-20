<?php

namespace Studio24\Agent;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Studio24\Agent\Exception\FailedHttpRequestException;
use Studio24\Agent\Traits\TypeTrait;
use Studio24\Agent\Traits\VerboseTrait;

class HttpClient
{
    use TypeTrait, VerboseTrait;

    const API_PING_URL = '/api/v1/ping';
    const API_SEND_DATA_URL = '/api/v1/update';
    const API_SEND_DEPLOYMENT_URL = '/api/v1/deployment';

    /** @var Client */
    private $client;

    /**
     * Constructor
     * @param string $endpointUrl
     * @param string $authToken
     */
    public function __construct($endpointUrl, $authToken, $basicAuth = null)
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
            ],
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
     * Send ping request to server
     * @return \Psr\Http\Message\ResponseInterface
     * @throws FailedHttpRequestException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ping()
    {
        $response = $this->request('GET', self::API_PING_URL);
        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send ping HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }
        return $response;
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
     * @param Agent $data
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendData($data)
    {
        $this->throwIfNotInstanceOf(Agent::class, 'data', $data);
        $response = $this->request('POST', self::API_SEND_DATA_URL, [
            'json' => $data->toJson()
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
        $response = $this->request('POST', self::API_SEND_DEPLOYMENT_URL, [
            'json' => $data
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new FailedHttpRequestException(sprintf('Failed to send sendData HTTP request, error %s', $response->getStatusCode() . ' ' . $response->getReasonPhrase()));
        }

        return $response;
    }

    /**
     * Make HTTP request, allows us to use verbose mode
     *
     * @param string $method
     * @param $uri
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, $uri = '', array $options = [])
    {
        if ($this->isVerbose()) {
            echo sprintf("%s %s", $method, $uri) . PHP_EOL;
            if (!empty($options["json"])) {
                echo sprintf("JSON data:", $options["json"]) . PHP_EOL;
                unset($options["json"]);
            }
            if (!empty($options)) {
                echo "Options:" . PHP_EOL;
                echo json_encode($options, JSON_PRETTY_PRINT) . PHP_EOL;
            }
        }
        return $this->client->request($method, $uri, $options);
    }

}
