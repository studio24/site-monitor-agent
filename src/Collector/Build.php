<?php

namespace Studio24\Agent\Collector;

class Build implements CollectorInterface
{

     /**
     * Constructor
     * @param null $wordPressBasePath
     */
    public function __construct($buildFile = null)
    {
        $this->buildFile = $buildFile;
    }

    /**
     * Return collector name
     * @return string
     */
    public function getName()
    {
        return 'Build';
    }

    /**
     * Collect data, should return an array of data
     * @return array
     */
    public function collectData()
    {
        return [
            'summary' => $this->getJson()
        ];
    }


    /**
     * Return build JSON data
     * @return array
     */
    protected function getJson()
    {
        $data = [];

	$filePath = 'https://www.studio24.net/'.$this->buildFile;

        if (!$build_summary = file_get_contents($filePath)) {
            return [];
        }

        $data = json_decode($build_summary, true);

        return $data;
        
    }
}
