<?php

namespace Studio24\Agent\Model;

use Studio24\Agent\Traits\TypeTrait;

/**
 * Represents a technology version that we attach to the data collector
 */
class Version
{
    use TypeTrait;

    protected $slug;
    protected $version;
    protected $parent = null;
    protected $error = null;

    public function __construct($slug)
    {
        $this->setSlug($slug);
    }

    /**
     * Get the technology slug
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the technology slug (e.g. wordpress)
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->throwIfNotString('slug', $slug);
        $this->slug = $slug;
    }

    /**
     * Get the parent technology slug
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent technology slug, used to group technology versions
     * @param string $parent
     */
    public function setParent($parent)
    {
        $this->throwIfNotString('parent', $parent);
        $this->parent = $parent;
    }

    /**
     * Whether a parent exists
     * @return bool
     */
    public function hasParent()
    {
        return ($this->parent !== null);
    }

    /**
     * Get the technology version
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the technology version (e.g. 1.0.0)
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->throwIfNotString('version', $version);
        $this->version = $version;
    }

    /**
     * Whether a version exists
     * @return bool
     */
    public function hasVersion()
    {
        return ($this->version !== null);
    }

    /**
     * Return the error message
     * @return ?string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set an error message
     *
     * Use this if there's been a problem generating the technology version
     * @param string $error
     */
    public function setError($error)
    {
        $this->throwIfNotString('error', $error);
        $this->error = $error;
    }

    /**
     * Whether an error exists
     * @return bool
     */
    public function hasError()
    {
        return ($this->error !== null);
    }

    /**
     * Return object as array
     * @return string[]
     */
    public function toArray()
    {
        $data = [
            'slug' => $this->getSlug()
        ];
        if ($this->hasVersion()) {
            $data['version'] = $this->getVersion();
        }
        if ($this->hasParent()) {
            $data['parent'] = $this->getParent();
        }
        if ($this->hasError()) {
            $data['error'] = $this->getError();
        }
        return $data;
    }
}
