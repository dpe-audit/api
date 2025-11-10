<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

abstract class LocalCommand extends Command
{
    public final const INPUT = '/data/diagnostics';
    public final const OUTPUT = '/data/simulations';
    public final const LOGS = '/data/logs';

    public function __construct(protected readonly string $projectDir)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::OPTIONAL);
        $this->addOption('start', null, InputOption::VALUE_OPTIONAL, 'Début');
        $this->addOption('end', null, InputOption::VALUE_OPTIONAL, 'Fin');
        $this->addOption('strict', null, InputOption::VALUE_NONE, 'Mode strict');
    }

    protected function load(InputInterface $input, OutputInterface $output): array|false
    {
        $readdir = $this->projectDir . self::INPUT;

        $search = $input->getArgument('id') ? $input->getArgument('id') . '.xml' : null;
        $start = ($value = $input->getOption('start')) ? (int) $value : null;
        $end = ($value = $input->getOption('end')) ? (int) $value : null;

        if (false === $entries = scandir($readdir)) {
            $output->writeln("Le dossier source n'existe pas");
            return false;
        }
        if ($search) {
            $entries = array_filter($entries, fn($entry) => $entry === $search);
        }
        $entries = array_map(fn($entry) => "{$readdir}/{$entry}", $entries);
        $entries = array_filter($entries, fn($path) => \is_file($path));
        $entries = array_values($entries);

        if ($start) {
            $entries = array_slice($entries, $start);
            $entries = array_values($entries);
        }
        if ($end) {
            $entries = array_slice($entries, 0, $end);
            $entries = array_values($entries);
        }
        return $entries;
    }
}
