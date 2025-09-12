<?php

namespace App\Command;

use App\Api\Audit\ComputeAuditHandler;
use App\Api\Audit\Model\Audit as Resource;
use App\Database\Opendata\XMLElement;
use App\Model\Audit\Audit;
use App\Serializer\Opendata\XMLAuditDeserializer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:audit:simulation',
    description: 'Simule les audits stockés localement au format XML',
    hidden: false,
)]
final class SimulationCommand extends Command
{
    public final const INPUT = '/data/audits';
    public final const OUTPUT = '/data/simulations';

    public function __construct(
        private readonly string $projectDir,
        private readonly LoggerInterface $logger,
        private readonly XMLAuditDeserializer $deserializer,
        private readonly ComputeAuditHandler $handler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $success = 0;
        $counter = 0;
        $readdir = $writedir = $this->projectDir;
        $readdir .= $input->getOption('read') ?? self::INPUT;
        $writedir .= $input->getOption('write') ?? self::OUTPUT;

        $search = $input->getArgument('numero_dpe') ? $input->getArgument('numero_dpe') . '.xml' : null;

        if (false === $entries = scandir($readdir)) {
            $output->writeln("Le dossier source n'existe pas");
            return Command::FAILURE;
        }

        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : count($entries);

        for ($i = 0; $i < $limit; $i++) {
            $filename = $entries[$i];
            $path = "{$readdir}/{$filename}";

            if (!\is_file($path)) {
                continue;
            }
            if ($search && $filename !== $search) {
                continue;
            }
            $counter++;
            $output->writeln("Processing {$counter}/{$limit} : {$filename}...");

            try {
                $xml = \simplexml_load_file($path, XMLElement::class);
                $audit = $this->deserializer->deserialize($xml);
                $success++;
            } catch (\Throwable $th) {
                $this->logger->debug($th, ['audit' => $filename]);

                if ($input->getOption('strict')) {
                    throw $th;
                    return Command::FAILURE;
                }
                continue;
            }

            $handle = $this->handler;
            $audit = $handle($audit);

            $this->save($audit);

            if ($limit && $counter === $limit) {
                break;
            }
        }
        $output->writeln("Done {$success}/{$limit}");
        return Command::SUCCESS;
    }

    private function save(Audit $entity): void
    {
        $resource = Resource::from($entity);
        file_put_contents("{$this->projectDir}/data/simulations/{$entity->id()}.json", json_encode($resource, JSON_UNESCAPED_UNICODE));
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::OPTIONAL, 'Numéro de DPE :');
        $this->addOption('read', 'r', InputOption::VALUE_REQUIRED, 'Dossier source');
        $this->addOption('write', 'w', InputOption::VALUE_REQUIRED, 'Dossier cible');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limite');
        $this->addOption('strict', 's', InputOption::VALUE_NONE, 'Mode strict');
    }
}
