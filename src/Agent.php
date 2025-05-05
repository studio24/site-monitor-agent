<?php

namespace Studio24\Agent;

use Studio24\Agent\Interfaces\ApplicationInterface;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Interfaces\VerboseInterface;
use Studio24\Agent\Model\VersionCollection;
use Studio24\Agent\Traits\ToJsonTrait;
use Studio24\Agent\Traits\VerboseTrait;

class Agent
{
    use VerboseTrait;
    use ToJsonTrait;

    private $siteId = null;
    private $url = null;
    private $environment = null;
    private $gitRepoUrl = null;

    private $account = null;
    private $serverName = null;

    /** @var CollectorInterface[] */
    private $collectors = [];

    /** @var VersionCollection */
    private $versions = null;

    public function setCollectors($collectors)
    {
        if (!is_array($collectors)) {
            throw new \InvalidArgumentException('$collectors argument must be an array');
        }
        foreach ($collectors as $collector) {
            if (!($collector instanceof CollectorInterface)) {
                Cli::error(sprintf("Data collector %s must implement Studio24\Agent\Collector\CollectorInterface", get_class($collector)));
                // @todo report error?
                continue;
            }
            if ($collector instanceof VerboseInterface) {
                $collector->setVerbose($this->isVerbose());
            }
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
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * @param string $serverName
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     * @return void
     */
    public function collectData()
    {
        $this->versions = new VersionCollection();

        /** @var CollectorInterface $collector */
        foreach ($this->collectors as $collector) {
            // Get data
            $collectedData = $collector->collectData();

            if (!($collectedData instanceof VersionCollection)) {
                Cli::error(sprintf("Data collector %s::collectData() does not return an instance of VersionCollection", get_class($collector)));
                // @todo report error
                continue;
            }

            $this->versions->merge($collectedData);

            // Optionally set environment and URL via collector
            if ($collector instanceof ApplicationInterface) {
                $environment = $collector->getEnvironment();
                if (is_string($environment) && !empty($environment)) {
                    $this->setEnvironment($environment);
                }
                $url = $collector->getUrl();
                if (is_string($url) && !empty($url)) {
                    $this->setUrl($url);
                }
            }
        }
    }

    /**
     * Return object as an array
     * @return array
     */
    public function toArray()
    {
        return [
            'environment'       => $this->getEnvironment(),
            'url'               => $this->getUrl(),
            'repo_url'          => $this->getGitRepoUrl(),
            'versions'          => $this->versions->toArray(),
        ];
    }
}
