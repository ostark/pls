<?php

use ostark\PackageLister\Package\Dependencies;

it('detects test library', function () {
    $dependencies = new Dependencies([
        'foo/bar',
        'phpunit/phpunit'
    ]);

    expect($dependencies->getTestPackage())->toBe('phpunit/phpunit');
});

it('detects test library and ignores versions', function () {
    $dependencies = new Dependencies([
        'foo/bar' => '1.0',
        'phpunit/phpunit' => '9.0'
    ]);

    expect($dependencies->getTestPackage())->toBe('phpunit/phpunit');
});


it('does not produce false-positives', function () {
    $dependencies = new Dependencies(['unknown/stuff','doesnot/matter']);

    expect($dependencies->getTestPackage())->toBeNull();
});

it('works with an empty array', function () {
    $dependencies = new Dependencies([]);

    expect($dependencies->getTestPackage())->toBeNull();
});
