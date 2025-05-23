<?php

namespace unit\Model;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Model\Version;

class VersionTest extends TestCase
{
    public function testVersion()
    {
        $version = new Version('foo');
        $version->setVersion('1.0.0');
        $data = $version->toArray();

        $this->assertEquals('foo', $data['slug']);
        $this->assertTrue($version->hasVersion());
        $this->assertFalse($version->hasParent());
        $this->assertFalse($version->hasError());
        $this->assertEquals('1.0.0', $data['version']);

        $version->setParent('test');
        $data = $version->toArray();
        $this->assertTrue($version->hasParent());
        $this->assertEquals('test', $data['parent']);
    }

    public function testInvalidSlug()
    {
        $this->expectException(\InvalidArgumentException::class);
        $version = new Version(['foo' => 'bar']);
    }

    public function testInvalidVersion()
    {
        $this->expectException(\InvalidArgumentException::class);
        $version = new Version('foo');
        $version->setVersion(1.0);
    }

    public function testInvalidParent()
    {
        $this->expectException(\InvalidArgumentException::class);
        $version = new Version('foo');
        $version->setParent(true);
    }

    public function testInvalidError()
    {
        $this->expectException(\InvalidArgumentException::class);
        $version = new Version('foo');
        $version->setError(-1);
    }
}
