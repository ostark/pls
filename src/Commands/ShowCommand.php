<?php

namespace ostark\PackageLister\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    protected static $defaultName = 'show';

    protected function configure(): void
    {
        $this->setDescription('Show packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('HERE');

        return Command::SUCCESS;

    }
}
