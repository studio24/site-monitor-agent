<?php

namespace unit\Traits;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Traits\VerboseTrait;

class VerboseTestClass
{
    use VerboseTrait;
}

class VerboseTraitTest extends TestCase
{
    public function testVerbose()
    {
        $object = new VerboseTestClass();
        $object->setVerbose(true);
        $this->assertTrue($object->isVerbose());

        $object->setVerbose(false);
        $this->assertFalse($object->isVerbose());

        // Casts passed argument to bool
        $object->setVerbose('test');
        $this->assertTrue($object->isVerbose());

        $object->setVerbose(0);
        $this->assertFalse($object->isVerbose());
    }
}
