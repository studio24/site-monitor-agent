# Site monitor agent
Agent to send data to Studio 24 site monitoring tool

See https://github.com/studio24/site-monitor

## Requirements:
* PHP 5.5+
* [Guzzle 6](https://docs.guzzlephp.org/en/6.5/)

## Tests

Run all tests (PHPUnit, PHP lint, phpcs) via:

```
composer test
```

Run PHPUnit tests via:

```
composer unit
```

This uses [PHPUnit polyfills](https://github.com/Yoast/PHPUnit-Polyfills) to help run PHPUnit tests on PHP 5.5 to 8. You 
can see an example of this in [CliTest.php](tests/CliTest.php) (see the `AssertStringContains` trait).
