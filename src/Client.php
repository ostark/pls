<?php

namespace ostark\PackageLister;

use GuzzleHttp\RequestOptions;
use Spatie\Packagist\PackagistClient as SpatieClient;
use Spatie\Packagist\PackagistUrlGenerator;

class Client extends SpatieClient
{
    public const TYPE_CRAFT = 'craft-plugin';

    static function make($userAgent = 'PackageLister'): static
    {
        $headers = ['User-Agent' => $userAgent];
        $client = new \GuzzleHttp\Client([RequestOptions::HEADERS => $headers]);
        $generator = new PackagistUrlGenerator();

        return new static($client, $generator);
    }
}
