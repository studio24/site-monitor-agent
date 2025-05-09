<?php

namespace integration\Collector;

use Studio24\Agent\Collector\Composer;
use Studio24\Agent\TempDirectory;
use Studio24\Agent\Test\AgentTestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class ComposerTest extends AgentTestCase
{
    use AssertStringContains;

    public function testComposer()
    {
        $collector = new Composer();
        $data = $collector->collectData();

        // This will only process the site-monitor-agent composer file since it's using the current autoloader
        $this->assertTrue($this->slugExists($data, 'guzzlehttp/guzzle'));
        $this->assertTrue($this->versionGreaterOrEqual('6.0.0', $this->getVersionBySlug($data, 'guzzlehttp/guzzle')));

        $version = $this->getBySlug($data, 'guzzlehttp/psr7');
        $this->assertTrue($this->versionGreaterOrEqual('2.0.0', $version->getVersion()));
        $this->assertStringContainsString('guzzlehttp/guzzle', $version->getParent());
    }

    public function testComposerTree()
    {
        $collector = new Composer();
        $tree = $collector->getComposerTree();

        $this->assertNull($tree["guzzlehttp/guzzle"]);
        $this->assertTrue(isset($tree["guzzlehttp/psr7"]));
        $this->assertStringContainsString('guzzlehttp/guzzle', $tree["guzzlehttp/psr7"]);
    }

    public function testExclude()
    {
        $collector = new Composer();
        $data = $collector->collectData();
        $this->assertTrue($this->slugExists($data, 'phpunit/phpunit'));

        $collector = new Composer();
        $collector->exclude('phpunit/phpunit');
        $data = $collector->collectData();
        $this->assertFalse($this->slugExists($data, 'phpunit/phpunit'));
    }

    public function testPlatformDependency()
    {
        $collector = new Composer();
        $data = $collector->collectData();
        $this->assertFalse($this->slugExists($data, 'php'));
        $this->assertFalse($this->slugExists($data, 'ext-json'));
    }
}
