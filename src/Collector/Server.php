<?php

namespace Studio24\Agent\Collector;

class Server implements CollectorInterface
{
    /**
     * Return collector name
     * @return string
     */
    public function getName()
    {
        return 'Server';
    }

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        return [
            'operating_system' => php_uname('s') . ' ' . php_uname('r'),
            'php_version' => phpversion(),
            'php_extensions' => get_loaded_extensions(),
            'disk_space' => $this->getDiskSpace()
        ];
    }

    /**
     * Collect total and free disk space
     * @return array
     */
    protected function getDiskSpace()
    {

        $disk_space = [];

        $mounted_volumes = ['/', '/data'];

        foreach ($mounted_volumes as $volume) {

            if (file_exists($volume)) {
                $disk_space["$volume"] = [
                    'total' => disk_total_space($volume),
                    'free' => disk_free_space($volume)
                ];
            }

        }

        return $disk_space;
    }

}
