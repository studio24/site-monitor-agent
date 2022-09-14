<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Config;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class ConfigTest extends TestCase {

    public function testParseConfig()
    {
        $config = new Config();
        $data = [
            'foo' => 'bar',
            'api_key' => '%API_KEY%',
            'environment' => '%ENVIRONMENT%',
        ];

        $this->assertEquals('bar', $data['foo']);
        $this->assertEquals('%API_KEY%', $data['api_key']);
        $this->assertEquals('%ENVIRONMENT%', $data['environment']);

        putenv('API_KEY=test');
        $data = $config->parseTokens(__DIR__ . '/config-test', $data);
        $this->assertEquals('test', $data['api_key']);
        $this->assertEquals('development', $data['environment']);
    }

}