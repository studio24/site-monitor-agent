<?php

namespace unit;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\TempDirectory;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class TempDirectoryTest extends TestCase
{
    /** @link https://github.com/Yoast/PHPUnit-Polyfills */
    use AssertStringContains;

    public function testTempDir()
    {
        $temp = new TempDirectory();
        $path = $temp->getTempDirectory('foobar');
        $this->assertTrue($temp->tempDirectoryExists($path));
        $this->assertStringContainsString(TempDirectory::TMP_FOLDER . '/foobar', $path);

        // Test writing
        file_put_contents($path . '/test.txt', 'Some test text');
        $this->assertTrue(file_exists($path . '/test.txt'));
        $this->assertEquals('Some test text', file_get_contents($path . '/test.txt'));

        // Test deleting
        $temp->deleteTempDirectory('foobar');
        $this->assertFalse(file_exists($path . '/test.txt'));
        $this->assertFalse($temp->tempDirectoryExists($path));
    }
}
