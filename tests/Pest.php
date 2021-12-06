<?php

use ostark\PackageLister\Package\Dependencies;
use ostark\PackageLister\Package\PackageCollection;
use ostark\PackageLister\Package\PluginPackage;

function validTestfilePath($file)
{
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file;
}

function dummyCollection($items = 1): PackageCollection
{
    $collection = new PackageCollection();
    foreach (range(1, $items) as $i) {
        $collection->add(new PluginPackage([
            'name' => "Dummy $i",
            'description' => "Dummy description $i",
            'repository' => "https://github.com/v/p-$i",
            'downloads' => 100 * $i,
            'dependents' => 10 * $i,
            'favers' => 100 * $i,
            'handle' => "dummy-$i",
            'version' => "dev-dummy-$i",
            'testLibrary' => null,
            'updated' => (new DateTime())->setTimestamp(1609462800),
        ]));
    }

   return $collection;
}
