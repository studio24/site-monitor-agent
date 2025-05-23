<?php

namespace integration\Collector;

use Studio24\Agent\Collector\Composer;
use Studio24\Agent\TempDirectory;
use Studio24\Agent\Test\AgentTestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class ComposerFoldersTest extends AgentTestCase
{
    private $path;

    /**
     * @before
     */
    protected function setUpFixtures()
    {
        parent::setUpFixtures();

        $this->temp = new TempDirectory();
        $this->path = $this->temp->getTempDirectory('composer');
        passthru(sprintf('./bin/tests/composer/install.sh %s', $this->path));
    }

    /**
     * @after
     */
    protected function tearDownFixtures()
    {
        // Any clean up needed related to `setUpFixtures()`.
        $this->temp->deleteTempDirectory('composer');

        parent::tearDownFixtures();
    }

    public function testSubFolder()
    {
        $collector = new Composer();
        $data = $collector->collectData();
        $this->assertFalse($this->slugExists($data, 'league/csv'));
        $this->assertFalse($this->slugExists($data, 'league/route'));

        $collector = new Composer($this->path);
        $data = $collector->collectData();
        $this->assertTrue($this->slugExists($data, 'league/csv'));
        $this->assertTrue($this->versionGreaterOrEqual('8.2.0', $this->getVersionBySlug($data, 'league/csv')));

        $collector = new Composer($this->path . '/folder');
        $data = $collector->collectData();
        $this->assertTrue($this->slugExists($data, 'league/route'));
        $this->assertTrue($this->versionGreaterOrEqual('3.1.0', $this->getVersionBySlug($data, 'league/route')));
    }

    public function testIgnoreRootPackage()
    {
        $collector = new Composer();
        $data = $collector->collectData();
        $this->assertFalse($this->slugExists($data, '__root__'));
    }
}
