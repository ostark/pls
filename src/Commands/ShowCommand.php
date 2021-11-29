<?php

namespace ostark\PackageLister\Commands;

use Illuminate\Support\Collection;
use ostark\PackageLister\FileHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    protected static $defaultName = 'show';


    protected function configure(): void
    {
        $this
            ->setDescription('Show packages')
            ->addOption('field', null, InputOption::VALUE_OPTIONAL, 'Sort table by this field', 'monthlyDownloads')
            ->addOption('direction')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Max items', 20)
            ->addOption('output', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new FileHelper(getcwd());
        $field = $input->getOption('field');
        $limit = $input->getOption('limit');
        $outputFile = $input->getOption('output');

        if (!$collection = $io->readJson(TEMP_JSON)) {
            $output->writeln("Dataset not found, use the 'generate' command first");
            return Command::FAILURE;
        }

        if ($outputFile) {
            $io->writeJson($outputFile, $collection);
            return Command::SUCCESS;
        }

        $sorted = $collection->sortBy(function ($package, $key) use ($field) {
            return $package->$field;
        }, SORT_REGULAR, true)->take($limit);

        $this->renderTable($sorted, $output);
        $output->writeln('Datasource from: ' . $io->getFileDate(TEMP_JSON)->format('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }

    private function renderTable(Collection $collection, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['name','downloads','dependents']);

        foreach ($collection as $package) {
            $table->addRow([$package->name, $package->monthlyDownloads, $package->dependents]);
        }

        $table->render();
    }
}
