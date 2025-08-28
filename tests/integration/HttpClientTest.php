<?php

namespace integration;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Exception\HttpException;
use Studio24\Agent\Exception\HttpNetworkException;
use Studio24\Agent\HttpClient;
use Studio24\Agent\Version;

class HttpClientTest extends TestCase
{
    public function testOK()
    {
        $client = new HttpClient('https://httpbin.org/', 'test-token');

        // Bearer auth
        $response = $client->get('/bearer');
        $this->assertEquals(200, $client->getHttpStatusCode());
        $this->assertEquals(true, $response["authenticated"]);

        // GET and user agent
        $response = $client->get('/get');
        $this->assertEquals(200, $client->getHttpStatusCode());
        $this->assertEquals(Version::getUserAgent(), $response["headers"]["User-Agent"]);

        // POST data
        $response = $client->post('/post', json_encode(['foo' => 'bar']));
        $this->assertEquals(200, $client->getHttpStatusCode());
        $this->assertEquals("bar", $response["json"]["foo"]);
    }

    public function testError404()
    {
        $this->expectException(HttpException::class);
        $client = new HttpClient('https://httpbin.org/', 'test-token');
        $client->get('/status/404');
    }

    public function testNetworkError()
    {
        $this->expectException(HttpNetworkException::class);
        $client = new HttpClient('https://localhost/', 'test-token');
        $client->get('/');
    }


}
