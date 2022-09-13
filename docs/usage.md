# Usage

## Help information
Return help information:

```
php ./vendor/bin/agent.php --help
```

## Setup configuration file

Copy example config file to your local project, if it does not already exist.
```
php ./vendor/bin/agent.php setup 
```

## Run in dry mode
Collect data and output to terminal (does not send any data):

```
php ./vendor/bin/agent.php
```

## Run and send data to API 
Send data to API endpoint:

```
php ./vendor/bin/agent.php send
```

## Verbose mode

Add `-v` to any command to output additional debugging information to the command line. This does not change the data 
that is sent to the API. 

```
php ./vendor/bin/agent.php -v
```

