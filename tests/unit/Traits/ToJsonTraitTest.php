<?php

namespace unit\Traits;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Traits\ToJsonTrait;

class JsonTestClass
{
    use ToJsonTrait;
}

class JsonTestClass2
{
    use ToJsonTrait;

    public function toArray()
    {
        return ['foo' => 'bar', 'test' => 24];
    }
}

class ToJsonTraitTest extends TestCase
{
    public function testMissingToArray()
    {
        $object = new JsonTestClass();
        $this->expectException(\BadFunctionCallException::class);
        $object->toJson();
    }

    public function testJson()
    {
        $object = new JsonTestClass2();
        $json = $object->toJson();
        $this->assertEquals('{"foo":"bar","test":24}', $json);
        $data = json_decode($json, true);
        $this->assertEquals(2, count($data));
        $this->assertEquals('bar', $data['foo']);
        $this->assertEquals(24, $data['test']);
    }
}
