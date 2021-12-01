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
}
