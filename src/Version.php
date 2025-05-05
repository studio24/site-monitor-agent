<?php

namespace Studio24\Agent;

use Composer\InstalledVersions;

final class Version
{
    const PACKAGE = 'studio24/site-monitor-agent';

    /**
     * Return current version of package
     *
     * Requires Composer 2, or returns null if not found
     *
     * @return string|null
     */
    public static function getVersion()
    {
        if (class_exists('\Composer\InstalledVersions')) {
            if (InstalledVersions::isInstalled(self::PACKAGE)) {
                return InstalledVersions::getPrettyVersion(self::PACKAGE);
            }
        }
        return null;
    }

    /**
     * Return the user agent string to use with HTTP requests
     *
     * @return string
     */
    public static function getUserAgent()
    {
        $version = self::getVersion();
        $version = $version ? '/' . $version : '';
        return 'Studio24_SiteMonitor' . $version . ' (+https://github.com/studio24/site-monitor-agent)';
    }
}
