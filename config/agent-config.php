<?php

namespace Studio24\Agent\Collector;

return [
    'apiBaseUrl'    => 'https://monitor.domain.tld/api/',
    'apiToken'      => 'TOKEN',
    'siteId'        => 'SITEID',
    'environment'   => 'production',
    'gitRepoUrl'    => 'https://github.com/studio24/site-monitor-agent',
    'collectors'    => [
        new Php(),
     ],
];
