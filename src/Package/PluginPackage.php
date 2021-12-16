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

        if (is_null($first['extra']['handle'] ?? null)) {
            return null;
        }

        $devDependencies = new Dependencies($first['require-dev'] ?? []);

        return new static([
                'name' =>$package['name'],
                'description' => $package['description'] ?? null,
                'repository' => $package['repository'],
                'downloads' => $package['downloads']['monthly'] ?? 0,
                'dependents' => $package['dependents'] ?? 0,
                'favers' => $package['favers'] ?? 0,
                'handle' => $first['extra']['handle'],
                'version' => $first['version'],
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
