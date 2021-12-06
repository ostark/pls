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
    protected string $pathToDataset;
    protected FileHelper $fileHelper;

    public function __construct(string $pathToDataset, FileHelper $fileHelper = null)
    {
        $this->pathToDataset = $pathToDataset;
        $this->fileHelper = $fileHelper ?: new FileHelper(getcwd());

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show packages')
            ->addOption('sortBy', null, InputOption::VALUE_OPTIONAL, 'Sort table by this field', 'downloads')
            ->addOption('sort', null, InputOption::VALUE_OPTIONAL, 'Sort order (ASC or DESC)', 'DESC')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Max items', 20)
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $field = $input->getOption('sortBy');
        $limit = $input->getOption('limit');
        $descending = strtoupper($input->getOption('sort')) === 'DESC';
        $outputFile = $input->getOption('output');

        if (!$collection = $this->fileHelper->readJson($this->pathToDataset)) {
            $output->writeln("Dataset not found, use the 'generate' command first");
            return Command::FAILURE;
        }

        if ($outputFile) {
            $this->fileHelper->writeJson($outputFile, $collection);
            return Command::SUCCESS;
        }

        $sorted = $collection->sortByField($field, $descending)->take($limit);
        $this->renderTable($sorted, $output, $field);

        return Command::SUCCESS;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $field = $input->getOption('sortBy');

        // Validate sortBy input
        if (!in_array($field, PluginPackage::SORT_OPTIONS)) {
            $output->writeln(sprintf(
                "Unsupported option for --sortBy, valid options are: %s",
                implode(', ', PluginPackage::SORT_OPTIONS)
            ));
            $this->setCode(fn() => Command::FAILURE);
        }

    }

    private function renderTable(PackageCollection $collection, OutputInterface $output, string $sortBy): void
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

        $output->writeln('Datasource from: ' . $this->fileHelper->getFileDate($this->pathToDataset)->format('Y-m-d H:i:s'));
        $output->writeln('Sorted by: ' . $sortBy);
    }
}
