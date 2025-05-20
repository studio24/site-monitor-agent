# Installation

Instructions are for the test version, see https://github.com/studio24/site-monitor-agent/pull/2

## Composer

Install via Composer:

```
composer require studio24/site-monitor-agent:dev-develop
```

## Configuration setup

Run this command in your project root folder to copy an example config file to your project:

```
php ./vendor/bin/agent.php setup 
```

See [configuration](configuration.md) for more details on config settings.

Testing:
* API URL: https://staging-monitor.studio24.net/api/v1

Test run:

```shell
php ./vendor/bin/agent.php send
```

How should we install this? Website cron (e.g. Laravel, WP Cron)? Or Unix cron? 