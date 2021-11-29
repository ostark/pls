<?php

namespace ostark\PackageLister\Commands;

use Illuminate\Support\Collection;
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
            ->addOption('field', null, InputOption::VALUE_OPTIONAL, 'Sort by this field', 'monthlyDownloads')
            ->addOption('direction')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Max items', 20)
            ->addOption('output', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $field = $input->getOption('field');
        $limit = $input->getOption('limit');
        $outputFile = $input->getOption('output');

        $collection = $this->readPackages();

        if ($outputFile) {
            $this->writeToFile($collection, $outputFile);
            return Command::SUCCESS;
        }

        $sorted = $collection->sortBy(function ($package, $key) use ($field) {
            return $package->$field;
        }, SORT_REGULAR, true)->take($limit);

        $this->renderTable($sorted, $output);

        return Command::SUCCESS;
    }

    /**
     * @param string $file
     *
     * @return \Illuminate\Support\Collection
     */
    private function readPackages($file = 'temp.json'): \Illuminate\Support\Collection
    {
        $collection = collect();
        $json = json_decode(file_get_contents('temp.json'));
        foreach ($json as $package) {
            $collection->add($package);
        }
        return $collection;
    }

    private function writeToFile(\Illuminate\Support\Collection $collection, string $file)
    {
        file_put_contents($file, $collection->toJson());
    }

    private function renderTable(Collection $collection, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['name','downloads']);

        foreach ($collection as $package) {
            $table->addRow([$package->name, $package->monthlyDownloads]);
        }

        $table->render();
    }
}
