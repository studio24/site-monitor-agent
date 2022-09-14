<?php

namespace Studio24\Agent\Collector;

return [
    'apiBaseUrl'    => 'https://monitor.domain.tld/api/',
    'apiToken'      => '%API_TOKEN%',
    'siteId'        => 'EXAMPLE',
    'environment'   => '%ENVIRONMENT%',
    'gitRepoUrl'    => 'https://github.com/studio24/site-monitor-agent',
    'collectors'    => [
        new Php(),
     ],
];
