# Architecture

A summary of the application architecture appears below.

## Constraints
The CLI script has to run on PHP 5.5+ due to some legacy clients Studio 24 help maintain. This is made possible 
by including [automated testing](contributing.md#continuous-integration) to help ensure code passes tests in a range 
of PHP versions (defined in `jobs.build.strategy.matrix.php-versions` in [php.yml](../.github/workflows/php.yml)).

## Directory structure

```
site-monitor-agent
├── bin
│   └── agent.php             -- CLI script, the entry-point to the application
├── config
│   └── agent-config.php      -- example config file
├── src                       -- the main source code
│   └── Collector/            -- data collectors
│       └── Php.php           -- example data collector class
│   └── Exception/            -- custom exceptions
│   └── Agent.php             -- responsible for collecting data and returning this
│   └── Cli.php               -- CLI utility functions
│   └── CommandRunner.php     -- functions to help run shell commands
│   └── Config.php            -- responsible for reading and storing config options
│   └── HttpClient.php        -- responsible for sending HTTP requests to API
└── tests/                    -- PHPUnit tests
```

## Application flow

The CLI script `bin/agent.php` is responsible for getting input and passing this to classes. Wherever possible, business 
logic should be pushed off to dedicated classes.



## Data collectors

A data collector must implement interface `Studio24\Agent\Collector\CollectorInterface`

See [writing data collectors](writing-collectors.md).

## A few things to bear in mind

### Checking types

We cannot use strong typing since this isn't supported in PHP 5.5. Where you expect the return of a function to be of a 
certain type please check this before using it. For example `Agent::setCollectors()` checks the argument is an array and that 
each element is of the correct class type before adding it.
