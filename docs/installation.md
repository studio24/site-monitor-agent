# Installation

> ![NOTE]
> Instructions are for the test version, see https://github.com/studio24/site-monitor-agent/pull/2

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

This creates an `agent-config.php` and `.env` file, but only if one does not already exist.

See [configuration](configuration.md) for more details on config settings.

### Adding an .env file to your project

If you have added an `.env` file to your project, make sure you gitignore this and add to shared_files in your deployment script.

.gitignore file:

```
.env
```

deploy.php file:

```shell
set('shared_files', [
    '.env'
]);
```

## Testing

You can test locally via:

```shell
php ./vendor/bin/agent.php

# Or via DDEV
ddev php ./vendor/bin/agent.php
```

This will output the data to be sent to the Site Monitor app in JSON format.

You can test the API token works and you can communicate with the Site Monitor app via:

```shell
php ./vendor/bin/agent.php ping
```

You should SSH to your server and test this remotely. You can do this via Deployer via:

```php
dep run "php ./vendor/bin/agent.php" staging
```

Once you have confirmed the site monitor agent works you can send data (on the remote server) via:

```php
php ./vendor/bin/agent.php send
```

Or via Deployer:

```shell
dep run "php ./vendor/bin/agent.php send" staging
```

Please only send data from the remote server, there is no point sending data from your local development environment.

## Automating the site monitor agent 

_TODO: We plan to add a Laravel and WordPress package to aid installing this on local cron._ 

It is recommended to run the Site Monitor agent hourly.

Cron example:

```
0 * * * * php /path/to/vendor/bin/agent.php send
```
