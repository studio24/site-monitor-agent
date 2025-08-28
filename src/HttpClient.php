<?php

namespace Studio24\Agent;

use Studio24\Agent\Exception\HttpException;
use Studio24\Agent\Exception\HttpNetworkException;
use Studio24\Agent\Exception\HttpRequestException;
use Studio24\Agent\Exception\JsonDecodeException;
use Studio24\Agent\Traits\TypeTrait;
use Studio24\Agent\Traits\VerboseTrait;

/**
 * Basic HTTP client using cURL
 */
class HttpClient
{
    use TypeTrait;
    use VerboseTrait;

    private $baseUri;
    private $headers;
    private $handle;

    /**
     * Set CURL default options
     * @link https://www.php.net/manual/en/curl.constants.php
     */
    private $curlDefaults = [
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 10
    ];

    /**
     * Constructor
     *
     * @param string $baseUri Base URL of API
     * @param ?string $authToken API authentication token
     */
    public function __construct($baseUri, $authToken = null)
    {
        $this->throwIfNotString('baseUri', $baseUri);
        $this->throwIfEmpty('baseUri', $baseUri);
        $this->throwIfNotString('authToken', $authToken);

        $this->baseUri = rtrim($baseUri, '/');
        $this->headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: ' . Version::getUserAgent(),
        ];
        if (null !== $authToken) {
            $this->headers[] = 'Authorization: Bearer ' . $authToken;
        }
    }

    public function __destruct()
    {
        if (is_resource($this->handle) || is_a($this->handle, 'CURLHandle')) {
            curl_close($this->handle);
        }
    }

    /**
     * Build URL from base URL and endpoint
     * @param string $endpointUrl
     * @return string
     */
    public function buildUrl($endpointUrl)
    {
        return $this->baseUri . '/' . ltrim($endpointUrl, '/');
    }

    /**
     * Send HTTP response via curl
     *
     * This creates a new cURL handle per request, which is OK here since we only run one HTTP request at a time
     *
     * @param string $method
     * @param string $endpointUrl
     * @param ?string $postData Response data to send with request
     * @return array Response data as an associative array
     * @throws HttpNetworkException
     * @throws HttpRequestException
     * @throws HttpException
     */
    private function sendRequest($method, $endpointUrl, $postData = null)
    {
        if ($method !== 'GET' && $method !== 'POST') {
            throw new \InvalidArgumentException('Method must be GET or POST');
        }
        $url = $this->buildUrl($endpointUrl);
        $this->handle = curl_init($url);
        curl_setopt_array($this->handle, $this->curlDefaults);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);

        // Verbose mode
        if ($this->isVerbose()) {
            echo $url . PHP_EOL;
            if (!empty($postData)) {
                echo sprintf("POST data: %s", $postData) . PHP_EOL;
            }
        }

        // Set POST request
        if ($method === 'POST') {
            curl_setopt($this->handle, CURLOPT_POST, true);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $postData);
        }

        // Send request
        $response = curl_exec($this->handle);

        // Detect error
        $errno = curl_errno($this->handle);
        switch ($errno) {
            case CURLE_OK:
                // Request OK
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new HttpNetworkException(sprintf('HTTP network error failed request to %s: %s', $url, curl_error($this->handle)), $errno);
            default:
                throw new HttpRequestException(sprintf('HTTP request error failed request to %s: %s', $url, curl_error($this->handle)), $errno);
        }

        // Is this a 200 response?
        $httpStatusCode = (int) $this->getHttpStatusCode();
        if ($httpStatusCode !== 200) {
            throw new HttpException(sprintf('HTTP status code %s failed request to %s', $httpStatusCode, $url), $httpStatusCode);
        }

        // Request OK
        $body = json_decode($response, true, 512);
        if (null === $body) {
            throw new JsonDecodeException('Cannot decode HTTP JSON response');
        }

        // Return JSON response as an associative array
        return $body;
    }

    /**
     * Return HTTP status code of last request
     * @return string
     */
    public function getHttpStatusCode()
    {
        return curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
    }

    /**
     * Send GET request
     * @param string $endpointUrl
     * @return array Decoded JSON response
     * @throws HttpNetworkException
     * @throws HttpRequestException
     */
    public function get($endpointUrl)
    {
        $this->throwIfNotString('endpointUrl', $endpointUrl);
        return $this->sendRequest('GET', $endpointUrl);
    }

    /**
     * Send POST request
     * @param string $endpointUrl
     * @param ?string $postData Response data to send with request
     * @return array Decoded JSON response
     * @throws HttpNetworkException
     * @throws HttpRequestException
     */
    public function post($endpointUrl, $postData = null)
    {
        $this->throwIfNotString('endpointUrl', $endpointUrl);
        $this->throwIfNotString('postData', $postData);
        return $this->sendRequest('POST', $endpointUrl, $postData);
    }

}
