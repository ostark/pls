<?php

namespace ostark\PackageLister;

use DateTime;
use ostark\PackageLister\Package\PackageCollection;
use ostark\PackageLister\Package\PluginPackage;

class FileHelper
{
    protected string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function writeJson(string $file, PackageCollection $collection): void
    {
        $file = $this->normalizePath($file);
        file_put_contents($file, $collection->toJson()) === false;
    }

    public function readJson(string $file): ?PackageCollection
    {
        if (!file_exists($this->normalizePath($file))) {
            return null;
        }

        $collection = new PackageCollection();
        $json = json_decode(file_get_contents($this->normalizePath($file)));

        foreach ($json as $package) {
            $collection->add(PluginPackage::createFromJsonObject($package));
        }

        return $collection;
    }

    public function getFileDate(string $file): ?DateTime
    {
        $file = $this->normalizePath($file);
        if (!file_exists($file)) {
            return null;
        }

        $dt = new DateTime();
        $dt->setTimestamp(filemtime($file));

        return $dt;
    }

    private function isAbsolutePath(string $file): bool
    {
        return str_starts_with($file, DIRECTORY_SEPARATOR);
    }

    private function normalizePath(string $file): string
    {
        $path = rtrim($this->basePath, DIRECTORY_SEPARATOR);
        return $this->isAbsolutePath($file) ? $file : $path . DIRECTORY_SEPARATOR . $file;
    }
}
