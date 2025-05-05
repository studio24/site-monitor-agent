<?php

namespace integration\Collector;

use Studio24\Agent\Collector\Php;
use Studio24\Agent\Test\AgentTestCase;

class PhpTest extends AgentTestCase
{
    public function testPhpVersion()
    {
        $version = phpversion();
        $php = new Php();
        $data = $php->collectData();

        $this->assertTrue($this->slugExists($data, 'php'));
        $this->assertEquals($version, $this->getVersionBySlug($data, 'php'));
    }
}
