# Site monitor agent
Agent to send data to Studio 24 site monitoring tool

See https://github.com/studio24/site-monitor

## Requirements:
* PHP 5.5+
* [Guzzle 6](https://docs.guzzlephp.org/en/6.5/)

## Installation

### Composer
Install via local copy of site-monitor-agent (use this during development):

Set the following to your composer.json with url pointing to your local install of site-monitor-agent:

```json
{
  "minimum-stability": "dev",
  "prefer-stable": true,
  "repositories": [
    {
      "type": "path",
      "url": "../../studio24/site-monitor-agent/"
    }
  ]
}
```

Install via Composer:

```
composer require studio24/site-monitor-agent
```

### Config file

The config file `agent-config.php` should be copied to your project base folder when you install the Composer 
package. You can alter config settings here.

Copy example config file to your project:

```
./vendor/bin/agent.php setup
```

Config settings: TODO

## Usage

Return help information:

```
php ./vendor/bin/agent.php --help
```

Collect data and output to terminal (does not send any data):

```
php ./vendor/bin/agent.php 
```

Send data to API endpoint:

```
php ./vendor/bin/agent.php send
```

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
