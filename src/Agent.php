<?php

namespace Studio24\Agent;

use Studio24\Agent\Collector\CollectorInterface;

class Agent
{
    private $siteId = null;
    private $environment = null;
    private $gitRepoUrl = null;

    /** @var CollectorInterface[] */
    private $collectors = [];

    /**
     * Constructor
     * @param $collectors
     */
    public function __construct($collectors)
    {
        $this->collectors = $collectors;
    }

    public function setCollectors($collectors)
    {
        if (!is_array($collectors)) {
            throw new \InvalidArgumentException('$collectors argument must be an array');
        }
        $this->collectors = $collectors;
    }

    /**
     * @return null
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param null $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return null
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param null $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return null
     */
    public function getGitRepoUrl()
    {
        return $this->gitRepoUrl;
    }

    /**
     * @param null $gitRepoUrl
     */
    public function setGitRepoUrl($gitRepoUrl)
    {
        $this->gitRepoUrl = $gitRepoUrl;
    }

    /**
     * Return array of data collected for this site
     * @return array
     */
    public function collectData()
    {
        $data = [];

        /** @var CollectorInterface $collector */
        foreach ($this->collectors as $collector) {
            $data[$collector->getName()] = $collector->collectData();
        }

        return [
            'site_id' => $this->siteId,
            'environment' => $this->environment,
            'git_repo' => $this->gitRepoUrl,
            'data' => $data,
        ];
    }
}
