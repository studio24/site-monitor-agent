<?php

namespace unit\Traits;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Traits\ApplicationTrait;

class AppTestClass
{
    use ApplicationTrait;
}

class ApplicationTraitTest extends TestCase
{
    public function testGettersSetters()
    {
        $object = new AppTestClass();
        $object->setEnvironment('production');
        $object->setUrl('http://www.example.com/');

        $this->assertEquals('production', $object->getEnvironment());
        $this->assertEquals('http://www.example.com/', $object->getUrl());
    }
}
