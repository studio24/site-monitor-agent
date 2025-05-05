<?php

namespace Studio24\Agent\Test;

use Studio24\Agent\Model\Version;
use Yoast\PHPUnitPolyfills\TestCases\XTestCase;

/**
 * Base class for integration tests
 *
 * We extend Yoast PHPUnitPolyfills TestCase to ensure compatibility with PHP versions <7.1
 * @see https://github.com/Yoast/PHPUnit-Polyfills?tab=readme-ov-file#option-2-yoastphpunitpolyfillstestcasesxtestcase
 */
abstract class AgentTestCase extends XTestCase
{
    /**
     * Test whether a slug appears in an array of collector data
     *
     * @param array $data
     * @param string $slug
     * @return bool
     */
    public function slugExists($data, $slug)
    {
        /** @var Version $item */
        foreach ($data as $item) {
            if ($item->getSlug() === $slug) {
                return true;
            }
        }
        return false;
    }

    /**
     * Test whether a version is greater or equal to the expected value
     *
     * @param $expected
     * @param $actual
     * @return bool
     */
    public function versionGreaterOrEqual($expected, $actual)
    {
        return version_compare($actual, $expected, '>=');
    }

    /**
     * Return version name in an array of collector data by slug
     *
     * @param array $data
     * @param string $slug
     * @return ?string
     */
    public function getVersionBySlug($data, $slug)
    {
        /** @var Version $item */
        foreach ($data as $item) {
            if ($item->getSlug() === $slug) {
                return $item->getVersion();
            }
        }
        return null;
    }

    /**
     * Return version name in an array of collector data by slug
     *
     * @param array $data
     * @param string $slug
     * @return ?Version
     */
    public function getBySlug($data, $slug)
    {
        /** @var Version $item */
        foreach ($data as $item) {
            if ($item->getSlug() === $slug) {
                return $item;
            }
        }
        return null;
    }
}
