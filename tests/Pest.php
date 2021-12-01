<?php

use ostark\PackageLister\Package\Dependencies;

function validTestfilePath($file)
{
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file;
}

function dummyCollection($items = 1)
{
    $collection = new \ostark\PackageLister\Package\PackageCollection();
    foreach (range(1, $items) as $i) {
        $collection->add(new \ostark\PackageLister\Package\PluginPackage([
            'name' => "Dummy $i",
            'description' => "Dummy description $i",
            'repository' => "https://github.com/v/p-$i",
            'downloads' => 100 * $i,
            'dependents' => 10 * $i,
            'favers' => 100 * $i,
            'handle' => "dummy-$i",
            'version' => "dev-dummy-$i",
            'testLibrary' => null,
            'updated' => new \DateTime(1609462800),
        ]));
    }

   return $collection;
}
