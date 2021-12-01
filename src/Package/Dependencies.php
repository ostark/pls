<?php

namespace ostark\PackageLister\Package;

class Dependencies
{
    protected const TEST_LIBS = [
        'codeception/codeception',
        'pestphp/pest',
        'phpunit/phpunit',
    ];

    protected array $dependencies;

    public function __construct(array $dependencies)
    {
        $isIndexed = array_values($dependencies) === $dependencies;
        $this->dependencies = $isIndexed
            ? $dependencies
            : array_keys($dependencies);
    }

    public function getTestPackage(): ?string
    {
        // Pick the first
        foreach ($this->dependencies as $package) {
            if (in_array($package, self::TEST_LIBS)) {
                return $package;
            }
        }

        return null;
    }
}
