<?php

namespace ostark\PackageLister\Package;

use Illuminate\Support\Collection;

/**
 * This class exist for type-hinting purpose only
 */
class PackageCollection extends Collection
{
    /**
     * The items contained in the collection.
     *
     * @var PluginPackage[]
     */
    protected $items = [];

    public function offsetGet($key): PluginPackage
    {
        return parent::offsetGet($key);
    }

    public function get($key, $default = null): PluginPackage
    {
        return parent::get($key, $default);
    }

    public function sortByField(string $field, bool $descending = true): static
    {
        return $this->sortBy(function ($package, $key) use ($field) {
            return $package->$field;
        }, SORT_REGULAR, $descending);
    }

}
