<?php

namespace unit\Traits;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Exception\CommandException;
use Studio24\Agent\Exec;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class ExecTest extends TestCase
{
    use AssertStringContains;

    public function testExec()
    {
        $output = Exec::exec('ls', __DIR__);
        $this->assertStringContainsString('ExecTraitTest.php', $output);
    }

    public function testMissing()
    {
        $this->expectException(CommandException::class);
        $output = Exec::exec('foobar');
    }
}
