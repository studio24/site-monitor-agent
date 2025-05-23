# Contributing

## Understanding the code

See:
* [Architecture](architecture.md)
* [Writing collectors](writing-collectors.md)

## Git branches

The `main` branch is protected, please work in feature branches and merge your work via pull requests. 

## Continuous integration

On any push to `main` or `develop` the [PHP](../.github/workflows/php.yml) workflow is run at GitHub which lints PHP, checks for coding standards (PSR-12), 
and runs any PHPUnit tests.

Run all tests (PHPUnit, PHP lint, phpcs) via:


```bash
composer test
```

You can run individual tests via:

```bash
# PHP unit tests
composer unit

# PHP lint
composer lint

# PHP coding standards
composer cs

# Fix coding standards (phpcbf)
composer fix
```

### PHPUnit-Polyfills

This project uses [PHPUnit polyfills](https://github.com/Yoast/PHPUnit-Polyfills) to help run PHPUnit tests on PHP 5.5 to 8. 
You can see an example of this in [CliTest.php](tests/CliTest.php) (see the `AssertStringContains` trait).

## Testing

Wherever possible please add unit tests for each class and method. 

It can be difficult to test some web applications (e.g. WordPress) without booting them up. Where it's not practical to 
add tests for data collectors interfacing with a web application please ensure you report any errors via 
`HttpClient::reportError()`

## Testing the agent in a local project

While developing you can install a local copy of `site-monitor-agent` to a project to test the agent:

Set the following to your composer.json with url pointing to your local install of `site-monitor-agent`:

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

Run:

```
composer require studio24/site-monitor-agent
```

From within your local project you can run the agent via:

```bash
php ./vendor/bin/agent.php -v
```
