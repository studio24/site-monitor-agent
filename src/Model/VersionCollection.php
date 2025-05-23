<?php

namespace Studio24\Agent\Model;

use Studio24\Agent\Traits\ToJsonTrait;
use Studio24\Agent\Traits\TypeTrait;

/**
 * Represents a collection of version data we want to send to the site monitor API
 */
class VersionCollection implements \Iterator, \Countable
{
    use TypeTrait;
    use ToJsonTrait;

    private $position = 0;
    private $data = [];

    /**
     * Add a technology version to the collector data
     *
     * Either add an instance of the Version class
     * Or add a slug and version string
     *
     * @param string|Version $slugOrObject
     * @param ?string $version
     * @param ?string $parent
     * @param ?string $error
     * @return void
     */
    public function add($slugOrObject, $version = null, $parent = null, $error = null)
    {
        if ($slugOrObject instanceof Version) {
            $this->data[] = $slugOrObject;
            return;
        }

        if (is_string($slugOrObject)) {
            $item = new Version($slugOrObject);
            if ($version !== null) {
                $item->setVersion($version);
            }
            if ($parent !== null) {
                $item->setParent($parent);
            }
            if ($error !== null) {
                $item->setError($error);
            }
            $this->data[] = $item;
        }
    }

    /**
     * Merge a new collection with this collection and return it
     * @param $newCollection
     * @return $this
     */
    public function merge($newCollection)
    {
        $this->throwIfNotInstanceOf(self::class, 'newCollection', $newCollection);

        /** @var Version $item */
        foreach ($newCollection as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * Return object as an array
     * @return array
     */
    public function toArray()
    {
        $data = [];

        /** @var Version $version */
        foreach ($this as $version) {
            $data[] = $version->toArray();
        }

        return $data;
    }

    /**
     * @return Version
     */
    public function current()
    {
        return $this->data[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count()
    {
        return count($this->data);
    }
}
