<?php

namespace integration;

use Studio24\Agent\Agent;
use Studio24\Agent\Collector\Composer;
use Studio24\Agent\Collector\Php;
use Studio24\Agent\Test\AgentTestCase;

class AgentTest extends AgentTestCase
{
    public function testAgent()
    {
        // @todo complete example to collect Composer data
        $agent = new Agent();
        $agent->setCollectors([
            new Php(),
            new Composer()
        ]);
        $agent->setSiteId(24);
        $agent->setEnvironment('production');
        $agent->setGitRepoUrl('https://github.com/studio24/site-monitor-agent');
        $agent->setUrl('https://www.example.com/');
        $agent->setAccount('ABC123');
        $agent->setServerName('servername.studio24.net');

        $agent->collectData();
        $data = $agent->toArray();

        $this->assertEquals('production', $data['environment']);
        $this->assertEquals('https://www.example.com/', $data['url']);
        $this->assertEquals('https://github.com/studio24/site-monitor-agent', $data['repo_url']);
        $this->assertEquals('ABC123', $data['account']);
        $this->assertEquals('servername.studio24.net', $data['server']);

        $versions = [];
        array_walk($data['versions'], function($value) use (&$versions) {
            $versions[] = $value['slug'];
        });
        $this->assertTrue(in_array('php', $versions));
        $this->assertTrue(in_array('guzzlehttp/guzzle', $versions));
    }
}
