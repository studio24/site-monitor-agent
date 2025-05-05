<?php

namespace integration\Collector;

use Studio24\Agent\Collector\Laravel;
use Studio24\Agent\TempDirectory;
use Studio24\Agent\Test\AgentTestCase;

class LaravelTest extends AgentTestCase
{
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
        $collector = new Laravel($this->path);
        $data = $collector->collectData();

        // This reads the Laravel version from the tmp folder
        $this->assertTrue($this->slugExists($data, 'laravel'));
        $this->assertTrue($this->versionGreaterOrEqual('5.2.0', $this->getVersionBySlug($data, 'laravel')));

        $this->assertTrue($this->slugExists($data, 'laravel/tinker'));
        $this->assertTrue($this->versionGreaterOrEqual('1.0.10', $this->getVersionBySlug($data, 'laravel/tinker')));

        // Dependencies
        $this->assertTrue($this->slugExists($data, 'symfony/console'));
        $this->assertTrue($this->versionGreaterOrEqual('2.8.0', $this->getVersionBySlug($data, 'symfony/console')));
        $this->assertTrue($this->slugExists($data, 'nesbot/carbon'));
        $this->assertTrue($this->versionGreaterOrEqual('1.20.0', $this->getVersionBySlug($data, 'nesbot/carbon')));
    }
}
