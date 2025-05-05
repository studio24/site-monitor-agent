<?php

namespace unit\Traits;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Exception\CommandException;
use Studio24\Agent\Traits\ExecTrait;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class ExecTestClass
{
    use ExecTrait;
}

class ExecTraitTest extends TestCase
{
    use AssertStringContains;

    public function testExec()
    {
        $object = new ExecTestClass();
        $output = $object->exec('ls', __DIR__);
        $this->assertStringContainsString('ExecTraitTest.php', $output);
    }

    public function testFailure()
    {
        $object = new ExecTestClass();
        $this->expectException(CommandException::class);
        $output = $object->exec('ls', '--foo');
    }

    public function testMissing()
    {
        $object = new ExecTestClass();
        $this->expectException(CommandException::class);
        $output = $object->exec('foobar');
    }
}
