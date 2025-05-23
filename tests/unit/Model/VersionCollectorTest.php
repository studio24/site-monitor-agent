<?php

namespace unit\Model;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Model\Version;
use Studio24\Agent\Model\VersionCollection;

class VersionCollectorTest extends TestCase
{
    public function testToJsonAndArray()
    {
        $data = new VersionCollection();
        $data->add('test', '1.0.0');
        $data->add('bar', null, null, 'Error generating version');

        $version = new Version('foo');
        $version->setVersion('2.0.0');
        $data->add($version);

        $data->add('bar2', '2.0.1', 'test');

        $array = $data->toArray();
        $this->assertEquals(4, count($array));
        $this->assertEquals('bar', $array[1]['slug']);
        $this->assertTrue(isset($array[1]['error']));
        $this->assertFalse(isset($array[1]['version']));
        $this->assertEquals('test', $array[3]['parent']);

        $json = $data->toJson();
        $jsonData = json_decode($json, true);
        $this->assertEquals('[{"slug":"test","version":"1.0.0"},{"slug":"bar","error":"Error generating version"},{"slug":"foo","version":"2.0.0"},{"slug":"bar2","version":"2.0.1","parent":"test"}]', $json);
        $this->assertEquals('test', $jsonData[0]['slug']);
        $this->assertEquals('1.0.0', $jsonData[0]['version']);
        $this->assertEquals('1.0.0', $jsonData[0]['version']);
        $this->assertEquals('1.0.0', $jsonData[0]['version']);
        $this->assertEquals('test', $jsonData[3]['parent']);
    }
}
