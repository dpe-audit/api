<?php

namespace App\Command;

use App\Domain\Diagnostic\Diagnostic;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Handler\Diagnostic\ComputeDiagnosticHandler;
use App\Legacy\Model\DPE;
use App\Legacy\Transformer\Diagnostic\DiagnosticTransformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:diagnostic:simulation',
    description: 'Simule les audits stockés localement au format XML',
    hidden: false,
)]
final class SimulationCommand extends Command
{
    public final const INPUT = '/data/diagnostics';
    public final const OUTPUT = '/data/simulations';

    private array $logs = [['id', 'key', 'origin', 'value', 'diff (%)']];

    public function __construct(
        private readonly string $projectDir,
        private readonly LoggerInterface $logger,
        private readonly DiagnosticTransformer $transformer,
        private readonly ComputeDiagnosticHandler $handler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('numero_dpe', InputArgument::OPTIONAL, 'Numéro de DPE :');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Limite');
        $this->addOption('strict', 's', InputOption::VALUE_NONE, 'Mode strict');
        $this->addOption('compare', 'c', InputOption::VALUE_NONE, 'Comparaison des résultats');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $readdir = $writedir = $this->projectDir;
        $readdir .= self::INPUT;
        $writedir .= self::OUTPUT;

        $search = $input->getArgument('numero_dpe') ? $input->getArgument('numero_dpe') . '.xml' : null;

        if (false === $entries = scandir($readdir)) {
            $output->writeln("Le dossier source n'existe pas");
            return Command::FAILURE;
        }
        $count = count($entries);
        $counter = 0;

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
            $entity = $this->handler->__invoke($payload);
            $id = basename($filename, '.xml');

            $this->save($id, $entity);
            $this->compare($id, $entity, $data);
        }
        $this->savelog();
        $output->writeln("Done {$counter}/{$count}");
        return Command::SUCCESS;
    }

    private function save($id, Diagnostic $entity): void
    {
        $dto = DiagnosticDto::from($entity);
        $data = $dto->__normalize();
        file_put_contents("{$this->projectDir}/data/simulations/{$id}.json", json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function savelog(): void
    {
        $lines = [];
        foreach ($this->logs as $log) {
            $lines[] = implode(';', $log);
        }
        file_put_contents("{$this->projectDir}/data/rapport.csv", implode("\n", $lines));
    }

    private function compare($id, Diagnostic $entity, DPE $data): void
    {
        $logement = $data->logement();
        $sortie = $logement->sortie;

        // Déperditions
        $deperdition = $sortie->deperdition;
        $this->compareItem($id, 'gv', $deperdition->gv(), $entity->enveloppe()->data()->deperditions->gv);
        $this->compareItem($id, 'dp', $deperdition->dp(), $entity->enveloppe()->data()->deperditions->dp);
        $this->compareItem($id, 'dp_murs', $deperdition->deperdition_mur, $entity->enveloppe()->data()->deperditions->dp_murs);
        $this->compareItem($id, 'dp_pb', $deperdition->deperdition_plancher_bas, $entity->enveloppe()->data()->deperditions->dp_planchers_bas);
        $this->compareItem($id, 'dp_ph', $deperdition->deperdition_plancher_haut, $entity->enveloppe()->data()->deperditions->dp_planchers_hauts);
        $this->compareItem($id, 'dp_baies', $deperdition->deperdition_baie_vitree, $entity->enveloppe()->data()->deperditions->dp_baies);
        $this->compareItem($id, 'dp_portes', $deperdition->deperdition_porte, $entity->enveloppe()->data()->deperditions->dp_portes);
        $this->compareItem($id, 'dr', $deperdition->dr(), $entity->enveloppe()->data()->deperditions->dr);
        $this->compareItem($id, 'pt', $deperdition->pt(), $entity->enveloppe()->data()->deperditions->pt);
        $this->compareItem($id, 'hvent', $deperdition->hvent, $entity->enveloppe()->data()->permeabilite->hvent);
        $this->compareItem($id, 'hperm', $deperdition->hperm, $entity->enveloppe()->data()->permeabilite->hperm);

        // Apports et besoins
        $apport_et_besoin = $sortie->apport_et_besoin;
        $this->compareItem($id, 'f', $apport_et_besoin->fraction_apport_gratuit_ch, $entity->enveloppe()->data()->apports->f);
        $this->compareItem($id, 'apport_interne', $apport_et_besoin->apport_interne_ch, $entity->enveloppe()->data()->apports->apport_interne);
        $this->compareItem($id, 'apport_solaire', $apport_et_besoin->apport_solaire_ch, $entity->enveloppe()->data()->apports->apport_solaire);
        $this->compareItem($id, 'apport_interne_fr', $apport_et_besoin->apport_interne_fr, $entity->enveloppe()->data()->apports->apport_interne_fr);
        $this->compareItem($id, 'apport_solaire_fr', $apport_et_besoin->apport_solaire_fr, $entity->enveloppe()->data()->apports->apport_solaire_fr);

        // Parois
        $enveloppe = $data->logement()->enveloppe;
        foreach ($entity->enveloppe()->murs() as $key => $paroi) {
            $this->compareItem($id, 's_mur', $enveloppe->mur_collection[$key]->surface(), $paroi->data()->sdep);
            $this->compareItem($id, 'b_mur', $enveloppe->mur_collection[$key]->b, $paroi->data()->b);
            $this->compareItem($id, 'u_mur', $enveloppe->mur_collection[$key]->u(), $paroi->data()->u);
        }
        foreach ($entity->enveloppe()->planchers_bas() as $key => $paroi) {
            $this->compareItem($id, 's_pb', $enveloppe->plancher_bas_collection[$key]->surface(), $paroi->data()->sdep);
            $this->compareItem($id, 'b_pb', $enveloppe->plancher_bas_collection[$key]->b, $paroi->data()->b);
            $this->compareItem($id, 'u_pb', $enveloppe->plancher_bas_collection[$key]->u(), $paroi->data()->u);
        }
        foreach ($entity->enveloppe()->planchers_hauts() as $key => $paroi) {
            $this->compareItem($id, 's_ph', $enveloppe->plancher_haut_collection[$key]->surface(), $paroi->data()->sdep);
            $this->compareItem($id, 'b_ph', $enveloppe->plancher_haut_collection[$key]->b, $paroi->data()->b);
            $this->compareItem($id, 'u_ph', $enveloppe->plancher_haut_collection[$key]->u(), $paroi->data()->u);
        }
        foreach ($entity->enveloppe()->baies() as $key => $paroi) {
            $this->compareItem($id, 's_baies', $enveloppe->baie_vitree_collection[$key]->surface(), $paroi->data()->sdep);
            $this->compareItem($id, 'b_baies', $enveloppe->baie_vitree_collection[$key]->b, $paroi->data()->b);
            $this->compareItem($id, 'u_baies', $enveloppe->baie_vitree_collection[$key]->u(), $paroi->data()->u);

            if ($value = $enveloppe->baie_vitree_collection[$key]->ug) {
                $this->compareItem($id, 'ug_baies', $value, $paroi->data()->ug);
            }
            if ($value = $enveloppe->baie_vitree_collection[$key]->uw) {
                $this->compareItem($id, 'uw_baies', $value, $paroi->data()->uw);
            }
        }
        foreach ($entity->enveloppe()->portes() as $key => $paroi) {
            $this->compareItem($id, 's_portes', $enveloppe->porte_collection[$key]->surface(), $paroi->data()->sdep);
            $this->compareItem($id, 'b_portes', $enveloppe->porte_collection[$key]->b, $paroi->data()->b);
            $this->compareItem($id, 'u_portes', $enveloppe->porte_collection[$key]->u(), $paroi->data()->u);
        }

        // Refroidissement
        foreach ($entity->refroidissement()->generateurs() as $key => $generateur) {
            $this->compareItem($id, 'eer', $logement->climatisation_collection[$key]->eer, $generateur->data()->eer);
        }

        // Besoins
        $this->compareItem($id, 'besoin_ch', $apport_et_besoin->besoin_ch, $entity->chauffage()->data()->bch);
        $this->compareItem($id, 'besoin_ecs', $apport_et_besoin->besoin_ecs, $entity->ecs()->data()->becs);
        if ($apport_et_besoin->besoin_fr) {
            $this->compareItem($id, 'besoin_fr', $apport_et_besoin->besoin_fr, $entity->refroidissement()->data()->bfr);
        }

        // Consommations
        $this->compareItem($id, 'cch', $sortie->ef_conso->conso_ch, $entity->chauffage()->data()->cef_ch);
        $this->compareItem($id, 'cecs', $sortie->ef_conso->conso_ecs, $entity->ecs()->data()->cef_ecs);
        $this->compareItem($id, 'cfr', $sortie->ef_conso->conso_fr, $entity->refroidissement()->data()->cef_fr);
        $this->compareItem($id, 'conso_eclairage', $sortie->ef_conso->conso_eclairage, $entity->eclairage()->data()->cef_ecl);

    }

    private function compareItem(string $id, string $key, mixed $origin, mixed $value): void
    {
        $diff = $origin ? round(($value - $origin) / $origin * 100, 2) : "n/a";
        $this->logs[] = [$id, $key, $origin, $value, $diff];
    }
}
