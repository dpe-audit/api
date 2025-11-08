<?php

namespace App\Command;

use App\Handler\Diagnostic\ComputeDiagnosticHandler;
use App\Legacy\Model\DPE;
use App\Legacy\Transformer\Diagnostic\DiagnosticTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:diagnostic:performance',
    description: 'Calcule de performance du moteur',
    hidden: false,
)]
final class PerformanceCommand extends Command
{
    public final const INPUT = '/data/diagnostics';

    public function __construct(
        private readonly string $projectDir,
        private readonly DiagnosticTransformer $transformer,
        private readonly ComputeDiagnosticHandler $handler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $counter = 100;
        $filename = $input->getArgument('numero_dpe') . '.xml';
        $path = $this->projectDir . self::INPUT . '/' . $filename;

        if (!\is_file($path)) {
            return Command::FAILURE;
        }

        $time = new \DateTime();
        $output->writeln("Processing...");

        for ($i = 0; $i < $counter; $i++) {
            $xml = \simplexml_load_file($path);
            $data = DPE::from($xml);
            $payload = $this->transformer->__invoke($data);
            $this->handler->__invoke($payload);
        }
        $timer = $time->diff(new \DateTime);
        $output->writeln("{$counter} simulations processed in {$timer->f} seconds");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::REQUIRED, 'Numéro de DPE :');
    }
}
