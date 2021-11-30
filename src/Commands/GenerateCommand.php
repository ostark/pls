<?php

namespace ostark\PackageLister\Commands;

use ostark\PackageLister\FileHelper;
use ostark\PackageLister\Package\PackageCollection;
use ostark\PackageLister\Package\PluginPackage;
use ostark\PackageLister\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected static $defaultName = 'generate';
    protected  Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate packages from API')
            ->setHelp('The command takes some time to execute, be patient.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $collection = new PackageCollection();

        // List packages by type
        $list = $this->client->getPackagesNamesByType(Client::TYPE_CRAFT);
        $count = count($list['packageNames']);

        if ($list && $count > 0) {

            ProgressBar::setFormatDefinition('minimal', 'Progress: %percent%%');
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat('minimal');
            $progressBar->start();

            foreach ($list['packageNames'] as $name) {

                $single = $this->client->getPackage($name);

                if ($single['package']['abandoned'] ?? false) {
                    $progressBar->advance();
                    continue;
                }

                if ($package = PluginPackage::createFromApiResponse($single['package'])) {
                    $collection->add($package);
                    $progressBar->advance();
                }
            }

            $progressBar->finish();
        }

        (new FileHelper(getcwd()))->writeJson(TEMP_JSON, $collection);

        return Command::SUCCESS;
    }
}
