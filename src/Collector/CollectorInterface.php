<?php

namespace Studio24\Agent\Collector;

/**
 * Base data collector interface
 */
interface CollectorInterface
{
    /**
     * Collect data, should return an array of data[slug, parent, version]
     *
     * E.g.
     * return [
     *     ['slug' => 'wordpress', 'version' => '6.7.2'],
     *     ['slug' => 'wordpress_seo', 'parent' => 'wordpress', 'version' => '24.9']
     * ];
     *
     * @return array
     */
    public function collectData();

}
