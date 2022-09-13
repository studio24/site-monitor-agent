# Site monitor agent
Agent to send data to the [Studio 24 site monitoring tool](https://github.com/studio24/site-monitor).

See [documentation](docs/README.md).

## Requirements
* PHP 5.5 to 8.1
* [Composer](https://getcomposer.org/)

## Installation

See [installation](docs/installation.md).

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
* Composer packages?
* Node version
* SSL certificates

## Credits
- **Simon R Jones** - *Lead Developer* - Studio 24
- **Gareth Trinkwon** - *Developer* - Studio 24
