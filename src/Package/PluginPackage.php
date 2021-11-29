<?php

namespace ostark\PackageLister\Package;

class PluginPackage implements \JsonSerializable
{
    public int $dependents;
    public ?string $description;
    public int $favers;
    public string $handle;
    public int $monthlyDownloads;
    public string $name;
    public string $repository;
    public ?string $testLibrary;
    public \DateTime $updated;

    public function __construct(array $args = [])
    {
        $this->mapArgumentsToClassProperties($args);
    }

    public static function createFromApiResponse(array $package): ?static
    {
        $versions = $package['versions'] ?? [];
        $first    = current($versions);

        if (!array_get($first, 'extra.handle')) {
            return null;
        }

       return new static([
                'name' =>  array_get($package, 'name'),
                'description' => array_get($package, 'description'),
                'repository' => array_get($package, 'repository'),
                'monthlyDownloads' => array_get($package, 'downloads.monthly'),
                'dependents' => array_get($package, 'dependents'),
                'favers' => array_get($package, 'favers'),
                'handle' => array_get($first, 'extra.handle'),
                'testLibrary' => (new Dependencies(array_get($first, 'require-dev', [])))->getTestPackage(),
                'updated' =>  new \DateTime($first['time']),
            ]
        );
    }

    public function jsonSerialize(): array
    {
        $fields            = (array)$this;
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
