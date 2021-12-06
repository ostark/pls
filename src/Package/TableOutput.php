<?php

namespace ostark\PackageLister\Package;

class TableOutput
{
    public const VISIBLE_FIELDS = [
        'handle',
        'repository',
        'downloads',
        'dependents',
        'favers',
        'testLibrary',
        'updated',
    ];

    protected PluginPackage $package;

    public function __construct(PluginPackage $package)
    {
        $this->package = $package;
    }

    public function getRow(): array
    {
        $row = [];
        $package = $this->package->jsonSerialize();

        foreach (self::VISIBLE_FIELDS as $field) {
            $row[] = $package[$field];
        }

        return $row;
    }
}
