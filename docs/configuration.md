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

Use the syntax `%NAME%` to read the value from an environment variable or an entry in a local `.env` file (in the same folder
as the config file `agent-config.php`).

### Example
For example, if you have the following in your config file:

```
    'apiToken'      => '%API_TOKEN%',
```

And the following content in your `.env` file:

```
API_TOKEN=ABC1234
```

This will first check if the environment variable `API_TOKEN` exists, and if so use it. If not, 
it will next check the `.env` file for the variable `API_TOKEN`. This will result in the config setting `'apiToken'` 
being set the value `ABC1234`. 

## Configuration options

TODO

### apiBaseUrl
The base URL of the API to send data to.

### apiToken
### siteId
### environment
### gitRepoUrl

## Data collectors

Data collectors are setup via the `collectors` option. 
