<?php

namespace integration\Collector;

use Studio24\Agent\Collector\Composer;
use Studio24\Agent\TempDirectory;
use Studio24\Agent\Test\AgentTestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class LaravelTest extends AgentTestCase
{
    use AssertStringContains;

    /** @var TempDirectory */
    protected $temp;

    /** @var string */
    protected $path;

    /**
     * @before
     */
    protected function setUpFixtures()
    {
        parent::setUpFixtures();

        $this->temp = new TempDirectory();
        $this->path = $this->temp->getTempDirectory('laravel');
        passthru(sprintf('./bin/tests/install-laravel.sh %s', $this->path));
    }

    /**
     * @after
     */
    protected function tearDownFixtures()
    {
        // Any clean up needed related to `setUpFixtures()`.
        $this->temp->deleteTempDirectory('laravel');

        parent::tearDownFixtures();
    }

    public function testLaravelCollector()
    {
        $collector = new Composer($this->path);
        $data = $collector->collectData();

        // This reads the Laravel version from the tmp folder
        $this->assertTrue($this->slugExists($data, 'laravel/framework'));
        $this->assertTrue($this->versionGreaterOrEqual('5.2.0', $this->getVersionBySlug($data, 'laravel/framework')));

        $this->assertTrue($this->slugExists($data, 'laravel/tinker'));
        $this->assertTrue($this->versionGreaterOrEqual('1.0.10', $this->getVersionBySlug($data, 'laravel/tinker')));

        // Dependencies
        $this->assertTrue($this->slugExists($data, 'symfony/console'));
        $version = $this->getBySlug($data, 'symfony/console');
        $this->assertTrue($this->versionGreaterOrEqual('2.8.0', $version->getVersion()));
        $this->assertStringContainsString('laravel/framework', $version->getParent());

        $this->assertTrue($this->slugExists($data, 'nesbot/carbon'));
        $version = $this->getBySlug($data, 'nesbot/carbon');
        $this->assertTrue($this->versionGreaterOrEqual('1.20.0', $version->getVersion()));
        $this->assertStringContainsString('laravel/framework', $version->getParent());
    }
}
