<?php

namespace ostark\PackageLister\Commands;

use ostark\PackageLister\FileHelper;
use ostark\PackageLister\Package\PackageCollection;
use ostark\PackageLister\Package\PluginPackage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    protected static $defaultName = 'show';
    protected string $tempJsonPath;

    public function __construct(string $tempJsonPath)
    {
        $this->tempJsonPath = $tempJsonPath;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show packages')
            ->addOption('sortBy', null, InputOption::VALUE_OPTIONAL, 'Sort table by this field', 'downloads')
            ->addOption('direction')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Max items', 20)
            ->addOption('output', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = new FileHelper(getcwd());
        $field = $input->getOption('sortBy');
        $limit = $input->getOption('limit');
        $outputFile = $input->getOption('output');

        if (!in_array($field, PluginPackage::SORT_OPTIONS)) {
            $output->writeln(sprintf(
                "Unsupported option for --sortBy, valid options are: %s",
                implode(', ', PluginPackage::SORT_OPTIONS)
            ));
            return Command::FAILURE;
        }

        if (!$collection = $file->readJson($this->tempJsonPath)) {
            $output->writeln("Dataset not found, use the 'generate' command first");
            return Command::FAILURE;
        }

        if ($outputFile) {
            $file->writeJson($outputFile, $collection);
            return Command::SUCCESS;
        }

        $sorted = $collection->sortBy(function ($package, $key) use ($field) {
            return $package->$field;
        }, SORT_REGULAR, true)->take($limit);

        $this->renderTable($sorted, $output);
        $output->writeln('Datasource from: ' . $file->getFileDate($this->tempJsonPath)->format('Y-m-d H:i:s'));
        $output->writeln('Sorted by: ' . $field);

        return Command::SUCCESS;
    }

    private function renderTable(PackageCollection $collection, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['name', 'version', 'downloads', 'dependents', 'test lib', 'updated']);

        foreach ($collection as $package) {
            $table->addRow([
                $package->name,
                $package->version,
                $package->downloads,
                $package->dependents,
                $package->testLibrary ?? '-',
                $package->updated
            ]);
        }

        $table->render();
    }
}
