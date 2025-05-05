<?php

namespace integration\Collector;

use Studio24\Agent\Collector\Composer;
use Studio24\Agent\Test\AgentTestCase;

class ComposerTest extends AgentTestCase
{
    public function testLaravelCollector()
    {
        $collector = new Composer();
        $data = $collector->collectData();

        // This will only process the site-monitor-agent composer file since it's using the current autoloader
        $this->assertTrue($this->slugExists($data, 'guzzlehttp/guzzle'));
        $this->assertTrue($this->versionGreaterOrEqual('6.0.0', $this->getVersionBySlug($data, 'guzzlehttp/guzzle')));
    }
}
