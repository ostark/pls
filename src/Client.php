<?php

namespace ostark\PackageLister;

use \Spatie\Packagist\PackagistClient as SpatieClient;

class Client extends SpatieClient
{
    public const TYPE_CRAFT = 'craft-plugin';

    static function make(): static
    {
        $client = new \GuzzleHttp\Client();
        $generator = new \Spatie\Packagist\PackagistUrlGenerator();

        return new static($client, $generator);
    }
}
