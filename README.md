# Site monitor agent
Agent to send data to the [Studio 24 site monitoring tool](https://github.com/studio24/site-monitor).

See [documentation](docs/README.md).

[![PHP tests](https://github.com/studio24/site-monitor-agent/actions/workflows/php.yml/badge.svg)](https://github.com/studio24/site-monitor-agent/actions/workflows/php.yml)

## Requirements
* PHP 5.6 to 8.4
* [Composer](https://getcomposer.org/)

## Installation

See [installation](docs/installation.md).

## TODO
- [ ] Do we need a version type? E.g. composer, node, etc
- [ ] Set aliases for a website (config)
- [ ] Set redirect URLs for a website (config)
- [ ] Collect Node package versions

## Usage

Collect data and run in dry-run mode (outputs data to send, but does not send any data to the API endpoint):

```bash
php ./vendor/bin/agent.php 
```

Collect data and send to API endpoint:

```bash
php ./vendor/bin/agent.php send
```

See [usage](docs/usage.md) for more details.

## Contributing

See [contributing](docs/contributing.md).

## Use cases

We expect to use this to collect data on things like:

* PHP version
* WordPress version, plugin versions
* Drupal version
* Craft CMS version
* Laravel version
* Composer packages
* Node version

## Tests

Run all tests:

```shell
./vendor/bin/phpunit
```

Run unit tests:

```shell
./vendor/bin/phpunit --testsuite unit
```

Run integration tests:

```shell
./vendor/bin/phpunit --testsuite integration
```

## Credits
- **Simon R Jones** - *Lead Developer* - Studio 24
- **Gareth Trinkwon** - *Developer* - Studio 24
