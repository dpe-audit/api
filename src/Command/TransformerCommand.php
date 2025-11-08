<?php

namespace App\Command;

use App\Legacy\Model\DPE;
use App\Legacy\Transformer\Diagnostic\DiagnosticTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:diagnostic:transformer',
    description: 'Transforme les diagnostics XML vers le nouveau modèle de données',
    hidden: false,
)]
final class TransformerCommand extends Command
{
    public final const INPUT = '/data/diagnostics';

    public function __construct(
        private readonly string $projectDir,
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly DiagnosticTransformer $transformer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::OPTIONAL, 'Numéro de DPE :');
        $this->addOption('strict', 's', InputOption::VALUE_NONE, 'Mode strict');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $readdir = $this->projectDir . self::INPUT;

        $search = $input->getArgument('numero_dpe') ? $input->getArgument('numero_dpe') . '.xml' : null;

        if (false === $entries = scandir($readdir)) {
            $output->writeln("Le dossier source n'existe pas");
            return Command::FAILURE;
        }
        $count = count($entries);
        $counter = 0;
        $success = 0;

        for ($i = 0; $i < $count; $i++) {
            $filename = $entries[$i];
            $path = "{$readdir}/{$filename}";

            if (!\is_file($path)) {
                continue;
            }
            if ($search && $filename !== $search) {
                continue;
            }
            $counter++;
            $output->writeln("Processing {$counter}/{$count} : {$filename}...");
            $xml = \simplexml_load_file($path);
            $data = DPE::from($xml);
            $payload = $this->transformer->__invoke($data);
            $errors = $this->validator->validate($payload);

            if (count($errors) > 0) {
                $output->writeln("Error");
                if ($input->getOption('strict')) {
                    var_dump(json_encode($payload->__normalize(), JSON_UNESCAPED_UNICODE));
                    dd((string) $errors);
                    return Command::FAILURE;
                }
                continue;
            }

            $success++;
        }
        $output->writeln("Done {$success}/{$counter}");
        return Command::SUCCESS;
    }
}
