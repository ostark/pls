<?php

namespace ostark\PackageLister\Package;

final class PluginPackage implements \JsonSerializable
{
    public const SORT_OPTIONS = ['dependents', 'favers', 'downloads', 'testLibrary', 'updated'];

    public string $name;
    public string $handle;
    public ?string $description;
    public string $version;
    public ?string $testLibrary;
    public int $dependents;
    public int $downloads;
    public int $favers;
    public string $repository;
    public \DateTime $updated;

    public function __construct(array $args = [])
    {
        $this->mapArgumentsToClassProperties($args);
    }

    public static function createFromApiResponse(array $package): ?static
    {
        $versions = $package['versions'] ?? [];
        $first = current($versions);

        if (!array_get($first, 'extra.handle')) {
            return null;
        }

        $devDependencies = new Dependencies(array_get($first, 'require-dev', []));

        return new static([
                'name' => array_get($package, 'name'),
                'description' => array_get($package, 'description'),
                'repository' => array_get($package, 'repository'),
                'downloads' => array_get($package, 'downloads.monthly'),
                'dependents' => array_get($package, 'dependents'),
                'favers' => array_get($package, 'favers'),
                'handle' => array_get($first, 'extra.handle'),
                'version' => array_get($first, 'version'),
                'testLibrary' => $devDependencies->getTestPackage(),
                'updated' => new \DateTime($first['time']),
            ]
        );
    }

    public static function createFromJsonObject(object $package): ?static
    {
        $fields = (array) $package;
        $fields['updated'] = \DateTime::createFromFormat('Y-m-d', $fields['updated']);

        return new static($fields);
    }

    public function jsonSerialize(): array
    {
        $fields = (array)$this;
        $fields['updated'] = $this->updated->format('Y-m-d');

        return $fields;
    }

    protected function mapArgumentsToClassProperties(array $args): void
    {
        $reflection = new \ReflectionClass($this);
        foreach ($args as $key => $value) {
            if ($reflection->getProperty($key)->isPublic()) {
                $this->{$key} = $value;
            }
        }
    }
}
