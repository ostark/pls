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
    protected Client $client;
    protected string $pathToDataset;
    protected FileHelper $fileHelper;

    public function __construct(Client $client, string $pathToDataset, FileHelper $fileHelper = null)
    {
        $this->client = $client;
        $this->pathToDataset = $pathToDataset;
        $this->fileHelper = $fileHelper ?: new FileHelper(getcwd());

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
        $count = $list ? count($list['packageNames']) : 0;

        if ($count > 0) {

            $progressBar = $this->createProgressBar($output, $count);
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

        $this->fileHelper->writeJson($this->pathToDataset, $collection);

        return Command::SUCCESS;
    }


    private function createProgressBar(OutputInterface $output, int $steps): ProgressBar
    {
        $lines = [
            '%bar% %percent:3s% %',
            'time:  %elapsed:6s%',
            'estimated:  %estimated:-6s%',
        ];

        $bar = new ProgressBar($output, $steps);
        $bar->setFormat(implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL);
        $bar->setBarWidth(80);

        $bar->setBarCharacter('<comment>▓</comment>');
        $bar->setEmptyBarCharacter('░');
        $bar->setProgressCharacter('');

        return $bar;
    }
}
