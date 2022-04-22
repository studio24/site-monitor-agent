<?php

namespace Studio24\Agent\Collector;

trait ApplicationTrait
{
    /** @var string */
    private $environment;

    /** @var string */
    private $url;

    /**
     * Return environment (usually production, staging, development)
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set environment
     * @param string $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Return website URL
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set website URL
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}
