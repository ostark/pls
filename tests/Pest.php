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
    $testLibsAndNullFills = Dependencies::TEST_LIBS + array_fill(0, 10, null);

    foreach (range(1, $items) as $i) {

        $collection->add(new PluginPackage([
            'name' => "Dummy $i",
            'description' => "Dummy description $i",
            'repository' => "https://github.com/v/p-$i",
            'downloads' =>  rand(1, $i * 100),
            'dependents' => rand(0, $i),
            'favers' => rand(1, $i * 20),
            'handle' => "dummy-$i",
            'version' => "dev-dummy-$i",
            'testLibrary' => $testLibsAndNullFills[array_rand($testLibsAndNullFills)],
            'updated' => (new DateTime())->setTimestamp(1609462800),
        ]));
    }

    return $collection;
}
