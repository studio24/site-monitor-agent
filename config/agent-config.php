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
