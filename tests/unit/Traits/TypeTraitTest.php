<?php

namespace unit\Traits;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Traits\TypeTrait;

class TypeTestClass
{
    use TypeTrait;
}

class TypeTraitTest extends TestCase
{
    public function testIsString()
    {
        $object = new TypeTestClass();
        $object->throwIfNotString('test', '24');

        // If we get here no exception
        $this->assertTrue(true);
    }

    public function testThrowIfNotString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $object = new TypeTestClass();
        $object->throwIfNotString('test', 24);
    }

    public function testIsArray()
    {
        $object = new TypeTestClass();
        $object->throwIfNotArray('test', ['foo' => 'bar', 'test' => 24]);

        // If we get here no exception
        $this->assertTrue(true);
    }

    public function testThrowIfNotArray()
    {
        $this->expectException(\InvalidArgumentException::class);
        $object = new TypeTestClass();
        $object->throwIfNotArray('test', true);
    }

    public function testIsInstanceOf()
    {
        $object = new TypeTestClass();
        $object->throwIfNotInstanceOf(TestCase::class, 'test', $this);

        // If we get here no exception
        $this->assertTrue(true);
    }

    public function testThrowIfNotInstanceOf()
    {
        $this->expectException(\InvalidArgumentException::class);
        $object = new TypeTestClass();
        $object->throwIfNotInstanceOf(TestCase::class, 'test', $object);
    }
}
