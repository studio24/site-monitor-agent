<?php

namespace Studio24\Agent\Traits;

trait ToJsonTrait
{
    /**
     * Return object as JSON, requires a toArray() method to return object as an array
     *
     * @param $prettyPrint
     * @return string
     */
    public function toJson($prettyPrint = false)
    {
        if (!method_exists($this, 'toArray')) {
            throw new \BadFunctionCallException(sprintf('The method toArray() must exist for this class %s', self::class));
        }

        if ($prettyPrint) {
            $json = json_encode($this->toArray(), JSON_PRETTY_PRINT);
        } else {
            $json = json_encode($this->toArray());
        }
        if (!$json) {
            throw new \UnexpectedValueException(sprintf('Cannot return object of class %s as JSON', self::class));
        }
        return $json;
    }
}
