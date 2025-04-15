<?php

namespace Studio24\Agent;

use Studio24\Agent\Interfaces\ApplicationInterface;
use Studio24\Agent\Interfaces\CollectorInterface;
use Studio24\Agent\Interfaces\VerboseInterface;
use Studio24\Agent\Traits\VerboseTrait;

class Agent
{
    use VerboseTrait;

    private $siteId = null;
    private $url = null;
    private $environment = null;
    private $gitRepoUrl = null;
    
    private $account = null;
    private $serverName = null;

    /** @var CollectorInterface[] */
    private $collectors = [];

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
            if ($collector instanceOf VerboseInterface) {
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
     * @return array
     */
    public function collectData()
    {
        $data = [];

        /** @var CollectorInterface $collector */
        foreach ($this->collectors as $collector) {
            
            // Get data
            $collectedData = $collector->collectData();

            if (!is_array($collectedData)) {
                Cli::error(sprintf("Data collector %s::getName() does not return an array", get_class($collector)));
                // @todo report error
                continue;
            }

            // Add the collected data to the $data array
            if (isset($collectedData['slug'])) {
                $data = array_merge($data, $collectedData);
            } else {
                $data = array_push($data, $collectedData);
            }

            // Optionally set environment and URL via collector
            // @todo is this required?
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

        return [
            'environment'       => $this->getEnvironment(),
            'url'               => $this->getUrl(),
            'hosting'           => [
                'account'       => $this->getAccount(),
                'server_name'   => $this->getServerName(),
            ],          
            'versions'          => $data,
        ];
    }
}
