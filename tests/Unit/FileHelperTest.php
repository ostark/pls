<?php

beforeEach(function () {
    @unlink(validTestfilePath('test.json'));
});

it('writes non-existing file', function () {
    $pathToFile = validTestfilePath('test.json');
    $helper = new \ostark\PackageLister\FileHelper(validTestfilePath(''));
    $helper->writeJson('test.json', dummyCollection(2));

    expect($pathToFile)->toBeFile();
});

it('overwrites existing file', function () {
    $pathToFile = validTestfilePath('test.json');
    $helper = new \ostark\PackageLister\FileHelper(validTestfilePath(''));
    $helper->writeJson('test.json', dummyCollection(1));
    $helper->writeJson('test.json', dummyCollection(3));

    $json = file_get_contents($pathToFile);

    expect($json)->json()->toHaveCount(3);
});

it('can read existing file and cast into collection', function () {
    $pathToFile = validTestfilePath('test.json');
    $helper = new \ostark\PackageLister\FileHelper(validTestfilePath(''));
    $helper->writeJson('test.json', dummyCollection(2));

    $collection = $helper->readJson($pathToFile);

    expect($collection)->toHaveCount(2);
});

it('does not use base path if absolute path is given', function () {
    $absPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test-abs.json';
    $helper = new \ostark\PackageLister\FileHelper(DIRECTORY_SEPARATOR);
    $helper->writeJson($absPath, dummyCollection(1));

    expect($absPath)->toBeFile();
});

it('returns null when reading non-existing file', function () {
    $helper = new \ostark\PackageLister\FileHelper(DIRECTORY_SEPARATOR);
    $collection = $helper->readJson(validTestfilePath('some-file.json'));
    expect($collection)->toBeNull();
});
