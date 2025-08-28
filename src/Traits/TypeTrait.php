<?php

namespace Studio24\Agent\Traits;

use Studio24\Agent\Exception\CommandException;

trait TypeTrait
{
    /**
     * Test whether the passed value is a string, throw an exception if not
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function throwIfNotString($name, $value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf("$%s must be a string, %s passed", $name, gettype($value)));
        }
    }

    /**
     * Test whether the passed value is not empty
     * @param $name
     * @param $value
     * @return void
     */
    public function throwIfEmpty($name, $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException(sprintf("$%s cannot be empty", $name));
        }
    }

    /**
     *  Test whether the passed value is an array, throw an exception if not
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function throwIfNotArray($name, $value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException(sprintf("$%s must be an array, %s passed", $name, gettype($value)));
        }
    }

    public function throwIfNotInstanceOf($className, $name, $value)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException(sprintf("$%s must be an object, %s passed", $name, gettype($value)));
        }
        if (!($value instanceof $className)) {
            throw new \InvalidArgumentException(sprintf("$%s must be an instance of %s, %s passed", $name, $className, get_class($value)));
        }
    }
}
