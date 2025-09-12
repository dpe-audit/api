<?php

namespace App\Engine\Rules\Deperdition;

use App\Engine\Input\Enveloppe\PontThermiqueInputRuleIterator;
use App\Engine\Table\PontThermiqueTableValeurRepository;

final class DeperditionPontThermiqueRule extends PontThermiqueInputRuleIterator
{
    public function __construct(
        protected PontThermiqueTableValeurRepository $repository,
    ) {}

    /**
     * Déperdition thermique en W/K
     */
    public function pt(): float
    {
        return $this->get('pt', function (): float {
            if ($this->item()->pont_thermique_negligeable()) {
                return 0;
            }
            $kpt = $this->kpt();
            $longueur = $this->item()->longueur();
            $pont_thermique_partiel = $this->item()->pont_thermique_partiel();
            return $kpt * $longueur * ($pont_thermique_partiel ? 0.5 : 1);
        });
    }

    /**
     * Valeur du pont thermique en W/K
     */
    public function kpt(): float
    {
        return $this->get('kpt', function () {
            return $this->item()->kpt_saisi() ?? $this->repository->kpt(
                type_liaison: $this->item()->type_liaison(),
                type_isolation_mur: $this->item()->type_isolation_mur(),
                type_isolation_plancher: $this->item()->type_isolation_plancher(),
                isolation_mur: $this->item()->isolation_mur(),
                isolation_plancher: $this->item()->isolation_plancher(),
                type_pose: $this->item()->type_pose(),
                presence_retour_isolation: $this->item()->presence_retour_isolation(),
                largeur_dormant: $this->item()->largeur_dormant(),
            ) ?? throw new \DomainException('Valeur forfaitaire kpt non trouvée');
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            kpt: $this->kpt(),
            pt: $this->pt()
        ));
    }
}
