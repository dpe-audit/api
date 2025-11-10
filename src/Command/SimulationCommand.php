<?php

namespace App\Command;

use App\Domain\Common\Enum\{Scenario, Usage};
use App\Domain\Diagnostic\Diagnostic;
use App\Dto\Diagnostic\DiagnosticDto;
use App\Handler\Diagnostic\ComputeDiagnosticHandler;
use App\Legacy\Model\DPE;
use App\Legacy\Transformer\Diagnostic\DiagnosticTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:diagnostic:simulation',
    description: 'Simule les audits stockés localement au format XML',
    hidden: false,
)]
final class SimulationCommand extends LocalCommand
{
    private array $logs = [['id', 'key', 'origin', 'value', 'diff (%)']];

    public function __construct(
        string $projectDir,
        private readonly DiagnosticTransformer $transformer,
        private readonly ValidatorInterface $validator,
        private readonly ComputeDiagnosticHandler $handler,
    ) {
        parent::__construct($projectDir);
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addOption('compare', null, InputOption::VALUE_NONE, 'Comparaison des résultats');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entries = $this->load($input, $output);
        $count = count($entries);
        $counter = 0;
        $timer = new \DateTime();

        foreach ($this->load($input, $output) as $entry) {
            $counter++;
            $id = basename($entry, '.xml');

            $output->writeln("Processing {$counter}/{$count} : {$id}...");

            $xml = simplexml_load_file($entry);
            $data = DPE::from($xml);
            $payload = $this->transformer->__invoke($data);
            //dd(json_encode($payload->__normalize(), JSON_UNESCAPED_UNICODE));
            $errors = $this->validator->validate($payload);

            if (count($errors) > 0) {
                continue;
            }

            $entity = $this->handler->__invoke($payload);

            if ($input->getOption('compare')) {
                $this->save($id, $entity);
                $this->compare($id, $entity, $data);
            }
        }
        $timer = $timer->diff(new \DateTime);
        $output->writeln("Done {$counter}/{$count} in {$timer->f} microseconds");

        if ($input->getOption('compare')) {
            $this->savelog();
        }
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
        $this->compareItem($id, 'conso_5_usages_m2', $sortie->ef_conso->conso_5_usages_m2, $entity->data()->bilan->cef);
        $this->compareItem($id, 'cch', $sortie->ef_conso->conso_ch, $entity->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::CHAUFFAGE));
        $this->compareItem($id, 'cecs', $sortie->ef_conso->conso_ecs, $entity->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::ECS));
        $this->compareItem($id, 'cfr', $sortie->ef_conso->conso_fr, $entity->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::REFROIDISSEMENT));
        $this->compareItem($id, 'cecl', $sortie->ef_conso->conso_eclairage, $entity->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::ECLAIRAGE));
        $this->compareItem($id, 'caux', $sortie->ef_conso->conso_totale_auxiliaire, $entity->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::AUXILIAIRE));
        $this->compareItem($id, 'caux_ch', $sortie->ef_conso->conso_auxiliaire_ch(), $entity->chauffage()->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::AUXILIAIRE));
        $this->compareItem($id, 'caux_ecs', $sortie->ef_conso->conso_auxiliaire_ecs(), $entity->ecs()->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::AUXILIAIRE));
        $this->compareItem($id, 'caux_fr', $sortie->ef_conso->conso_auxiliaire_fr(), $entity->refroidissement()->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::REFROIDISSEMENT));
        $this->compareItem($id, 'caux_ventilation', $sortie->ef_conso->conso_auxiliaire_ventilation, $entity->ventilation()->data()->consommations->cef(scenario: Scenario::CONVENTIONNEL, usage: Usage::AUXILIAIRE));
    }

    private function compareItem(string $id, string $key, mixed $origin, mixed $value): void
    {
        if ($origin === $value) {
            $diff = 0;
        } else {
            $diff = $origin ? round(($value - $origin) / $origin * 100, 2) : "n/a";
        }
        $this->logs[] = [$id, $key, $origin, $value, $diff];
    }
}
