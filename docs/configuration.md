# Configuration

Configuration is managed via a simple PHP file  called `agent-config.php` in your local project root folder.

## Installation
You can copy the example config file to your project via:

```bash
./vendor/bin/agent.php setup
```

or manually copy this from [agent-config.php](../config/agent-config.php)

## Setting config options via environment variables

It is recommended sensitive config values are not stored in a file committed to Git but are either set as 
environment variables or added to a `.env` file in your project root folder.

The Site Monitor agent will read environment variables or settings directly stored in an `.env` file.

Use the syntax `%NAME%` to read the value from an environment variable or an entry in a local `.env` file (in the same folder
as the config file `agent-config.php`).

You can use any environment variable names. The examples in the supplied config file are prefixed `SITE_MONITOR_` to help avoid 
any clashes with existing environment variables.

### Example
Example config file:

```php
<?php

namespace Studio24\Agent\Collector;

return [
    'apiBaseUrl'    => 'https://ddev-site-monitor-web/',
    'apiToken'      => '%SITE_MONITOR_TOKEN%',
    'environment'   => '%SITE_MONITOR_ENVIRONMENT%',
    'url'           => '%SITE_MONITOR_URL%',
    'gitRepoUrl'    => 'https://github.com/studio24/site-monitor-agent',
    'collectors'    => [
        new Php(),
        new Composer(),
     ],
];
```

And the following content in your `.env` file:

```
SITE_MONITOR_TOKEN=ABC1234
```

This will first check if the environment variable `SITE_MONITOR_TOKEN` exists, and if so use it. If not, 
it will next check the `.env` file for the variable `SITE_MONITOR_TOKEN`. This will result in the config setting `'apiToken'` 
being set the value `ABC1234`. 

## Configuration options

TODO

### apiBaseUrl
The base URL of the API to send data to. 

```php
    'apiBaseUrl'    => 'https://staging-monitor.studio24.net/',
```

### apiToken
The API token, you can copy this from the project page in the Site Monitor app. We recommend storing this in `.env` and not committing this to version control.

```php
    'apiToken'    => '%SITE_MONITOR_TOKEN%',
```

### environment

```php
    'environment'   => '%SITE_MONITOR_ENVIRONMENT%',
```

You may already have the environment set for your website. It is recommended to use the existing website environment name if one exists. 

For example:

* Craft CMS `CRAFT_ENVIRONMENT`
* Laravel or Symfony `APP_ENV`
 
```php
    'environment'   => '%APP_ENV%',
```

### url

```php
    'gitRepoUrl'    => 'https://github.com/studio24/site-monitor-agent',
```

### gitRepoUrl

The URL of the website the site monitor agent is running for. This will be unique per environment, so it is recommended to set this via an environment variable.
If you have an existing environment variable with the full HTTPS URL of the website, you can use this.

```php
    'url'           => '%SITE_MONITOR_URL%',
```

## Data collectors

Data collectors collect data to be sent to the Site Monitor tool. 

You define which collectors run by instantiating collector classes and assigning these to the 'collectors' array.

You can set as many collectors as you wish.

For example, to collect PHP data:

```php
    'collectors'    => [
        new Php(),
     ],
```

Some collectors allow you to pass the base path which defines where this software is installed, for example for WordPress:

```php
    'collectors'    => [
        new Php(),
        new WordPress('web'),
     ],
```

For software using Composer, you can just use the Composer collector.

```php
    'collectors'    => [
        new Php(),
        new Compser(),
     ],
```

> [!WARNING]
Please note installing Composer in multiple paths is risky and not recommended, since you can have clashes between packages.
 
You can add multiple Composer collectors, if you have Composer installed in sub-paths.

```php
    'collectors'    => [
        new Php(),
        new Compser(),
        new Compser('web/wp-content/plugins/my-plugin'),
     ],
```
