<?php

namespace App\Command;

use App\Legacy\Model\DPE;
use App\Legacy\Transformer\Diagnostic\DiagnosticTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:diagnostic:transformer',
    description: 'Transforme les diagnostics XML vers le nouveau modèle de données',
    hidden: false,
)]
final class TransformerCommand extends LocalCommand
{
    public function __construct(
        string $projectDir,
        private readonly DiagnosticTransformer $transformer,
        private readonly ValidatorInterface $validator,
    ) {
        parent::__construct($projectDir);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entries = $this->load($input, $output);
        $count = count($entries);
        $success = 0;
        $counter = 0;

        foreach ($this->load($input, $output) as $entry) {
            $counter++;
            $id = basename($entry, '.xml');

            $output->writeln("Processing {$counter}/{$count} : {$id}");

            $xml = simplexml_load_file($entry);
            $data = DPE::from($xml);
            $payload = $this->transformer->__invoke($data);
            $errors = $this->validator->validate($payload);

            if (count($errors) > 0) {
                $output->writeln("Error");

                if ($input->getOption('strict')) {
                    $json = json_encode($payload->__normalize(), JSON_UNESCAPED_UNICODE);
                    $output->writeln((string) $errors);
                    $output->writeln($json);
                    return false;
                }
                continue;
            }

            $success++;
        }
        $output->writeln("Done {$success}/{$counter}");
        return Command::SUCCESS;
    }
}
