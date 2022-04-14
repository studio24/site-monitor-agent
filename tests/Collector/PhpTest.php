<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Collector\Php;

class PhpTest extends TestCase
{
    public function testName()
    {
        $php = new Php();
        $this->assertEquals('PHP', $php->getName());
    }

    public function testPhpVersion()
    {
        $version = phpversion();
        $php = new Php();
        $data = $php->collectData();
        $this->assertEquals($version, $data['version']);
    }
}
