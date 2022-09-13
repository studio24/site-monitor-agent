# Site monitoring agent

Agent to send data to Studio 24 site monitoring tool.

## Project goals

The aim of the project is to collect data about websites managed by Studio 24 and send this to the 
[site monitoring web application](https://github.com/studio24/site-monitor) (via an API). 

The data collector must only collect data about the website or web application and should not 
run any checks or change any data locally. Running checks (e.g. are CMS plugins up-to-date) is the responsibility 
of the site monitoring web application.

## Documentation

* [Installation](installation.md)
* [Usage](usage.md)
* [Configuration](configuration.md)
* [Contributing](contributing.md) (making changes to this project)
    * [Architecture](architecture.md)
    * [Writing collectors](writing-collectors.md)