<?php

namespace ostark\PackageLister\Commands;

use ostark\PackageLister\Package\PluginPackage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected static $defaultName = 'generate';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate packages from API')
            ->setHelp('The command takes some time to execute, be patient.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $client = new \GuzzleHttp\Client();
        $generator = new \Spatie\Packagist\PackagistUrlGenerator();
        $packagist = new \Spatie\Packagist\PackagistClient($client, $generator);

        // List packages by type.
        $list = $packagist->getPackagesNamesByType('craft-plugin');
        $collection = collect();



        if ($list && count($list['packageNames'])) {

            foreach ($list['packageNames'] as $name) {

                $single = $packagist->getPackage($name);

                $versions = array_get($single, 'package.versions');
                $first = current($versions);
                $devPacks = implode('+', array_keys(array_get($first, 'require-dev', [])));

                if (array_get($single, 'package.abandoned')) {
                    continue;
                }

                if ($name === 'adigital/x-clacks-overhead') {
                    break;
                }

                $collection->add(
                    new PluginPackage(
                        array_get($single, 'package.name'),
                        array_get($single, 'package.description'),
                        array_get($first, 'extra.handle'),
                        array_get($single, 'package.repository'),
                        $devPacks,
                        array_get($single, 'package.downloads.monthly'),
                        array_get($single, 'package.dependents'),
                        array_get($single, 'package.favers'),
                        new \DateTime($first['time'])
                    )
                );
            }

        }

        dd($collection->toJson());


        $output->writeln('HERE');

        return Command::SUCCESS;

    }
}
