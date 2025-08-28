<?php

namespace Studio24\Agent;

use Studio24\Agent\Exception\TempDirectoryException;

class TempDirectory
{
    const TMP_FOLDER = 'SiteMonitorAgent';

    /** @var string|null */
    protected $tempDirectoryPath = null;

    public function getFullPath($name)
    {
        if (null !== $this->tempDirectoryPath) {
            return $this->tempDirectoryPath;
        }

        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException('Directory name must be passed as a string');
        }
        if (preg_match('/[^A-Za-z0-9_\-]/', $name)) {
            throw new \InvalidArgumentException(sprintf('Directory name "%s" must only contain characters A-Z, a-z, 0-9, _ or -', $name));
        }

        $this->tempDirectoryPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::TMP_FOLDER . DIRECTORY_SEPARATOR . $name;
        return $this->tempDirectoryPath;
    }

    public function tempDirectoryExists($name)
    {
        $fullPath = $this->getFullPath($name);
        return is_dir($fullPath);
    }

    /**
     * Create a temp directory
     * @param $name
     * @return string
     * @throws TempDirectoryException
     */
    public function getTempDirectory($name)
    {
        if ($this->tempDirectoryExists($name)) {
            return $this->getFullPath($name);
        }

        $fullPath = $this->getFullPath($name);
        if (!mkdir($fullPath, 0777, true)) {
            throw new TempDirectoryException(sprintf('Cannot create temp directory at %s', $fullPath));
        }

        return $fullPath;
    }

    public function deleteTempDirectory($name)
    {
        Exec::exec('rm', sprintf('-Rf %s', $this->getFullPath($name)));
    }
}
