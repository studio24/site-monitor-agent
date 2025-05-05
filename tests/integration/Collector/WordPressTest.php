<?php

namespace integration\Collector;

use Studio24\Agent\Collector\WordPress;
use Studio24\Agent\TempDirectory;
use Studio24\Agent\Test\AgentTestCase;

class WordPressTest extends AgentTestCase
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
        $this->path = $this->temp->getTempDirectory('wordpress');
        passthru(sprintf('./bin/tests/install-wordpress.sh %s', $this->path));
    }

    /**
     * @after
     */
    protected function tearDownFixtures()
    {
        // Any clean up needed related to `setUpFixtures()`.
        $this->temp->deleteTempDirectory('wordpress');

        parent::tearDownFixtures();
    }

    public function testWordPressCollector()
    {
        $collector = new WordPress($this->path);
        $data = $collector->collectData();

        // This reads the WordPress version from the tmp folder
        $this->assertTrue($this->slugExists($data, 'wordpress'));
        $this->assertTrue($this->versionGreaterOrEqual('6.0', $this->getVersionBySlug($data, 'wordpress')));
        $this->assertEmpty($this->getBySlug($data, 'wordpress')->getError());

        $this->assertTrue($this->slugExists($data, 'advanced-custom-fields'));
        $this->assertTrue($this->slugExists($data, 'classic-editor'));
        $this->assertTrue($this->slugExists($data, 'wordpress-seo'));

        $this->assertTrue($this->versionGreaterOrEqual('6.0.0', $this->getVersionBySlug($data, 'advanced-custom-fields')));
        $this->assertTrue($this->versionGreaterOrEqual('1.6.0', $this->getVersionBySlug($data, 'classic-editor')));
        $this->assertTrue($this->versionGreaterOrEqual('24.0', $this->getVersionBySlug($data, 'wordpress-seo')));

        // Test failing to find WordPress
        $collector = new WordPress('./');
        $data = $collector->collectData();
        $this->assertTrue($this->slugExists($data, 'wordpress'));
        $this->assertNotEmpty($this->getBySlug($data, 'wordpress')->getError());
    }
}
