<?php

namespace Studio24\Agent\Collector;

return [
    'apiBaseUrl'    => 'https://site-monitor.ddev.site/api/v1',
    'apiToken'      => '%MONITOR_API_TOKEN%',
    'environment'   => '%ENVIRONMENT%',
    'gitRepoUrl'    => 'https://github.com/studio24/site-monitor-agent',
    'url'           => '%MONITOR_URL%',
    'collectors'    => [
        new Php(),
        new Composer(),
        new Wordpress(),
     ],
];
