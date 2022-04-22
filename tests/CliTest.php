<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Studio24\Agent\Cli;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;

class CliTest extends TestCase
{
    /** @link https://github.com/Yoast/PHPUnit-Polyfills */
    use AssertStringContains;

    public function testTitle()
    {
        ob_start();
        $cli = new Cli(0, [], 'My name');
        $output = ob_get_clean();

        $this->assertEquals(0, $cli->argc);
        $this->assertEquals([], $cli->argv);
        $this->assertStringContainsString('My name', $output);

        ob_start();
        Cli::title('My new title');
        $output = ob_get_clean();
        $this->assertStringContainsString('My new title', $output);
    }

    public function testError()
    {
        $cli = new Cli(0, []);
        ob_start();
        Cli::error('My error message');
        $output = ob_get_clean();
        $this->assertStringContainsString('My error message', $output);
    }

    public function testHelpOption()
    {
        ob_start();
        $cli = new Cli(1, ['script.php']);
        $cli->help('My description', 'php bin/usage.php [options]', ['test' => 'test description'], false);
        $output = ob_get_clean();
        $this->assertStringNotContainsString('My description', $output);

        ob_start();
        $cli = new Cli(2, ['script.php', '--help']);
        $cli->help('My description', 'php bin/usage.php [options]', ['test' => 'test description'], false);
        $output = ob_get_clean();
        $this->assertStringContainsString('My description', $output);
        $this->assertStringContainsString('php bin/usage.php [options]', $output);

        $expected = 'test' . str_pad('', Cli::ARGUMENT_SPACING - 4, ' ') . 'test description';
        $this->assertStringContainsString($expected, $output);
    }

    public function testGetArgument()
    {
        $cli = new Cli(2, ['script.php', 'testing']);

        $this->assertEquals('testing', $cli->getArgument(1));
        $this->assertNull($cli->getArgument(2));
    }
}
