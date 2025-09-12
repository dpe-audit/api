<?php

namespace App\Engine\Rules\Apport\FacteurSolaire;

use App\Engine\Input\Enveloppe\DoubleFenetreInputRuleIterator;
use App\Engine\Table\DoubleFenetreTableValeurRepository;

final class FacteurSolaireDoubleFenetreRule extends DoubleFenetreInputRuleIterator
{
    public function __construct(
        private DoubleFenetreTableValeurRepository $repository,
    ) {}

    /**
     * Proportion d’énergie solaire incidente solaire de la double fenêtre
     */
    public function sw(): float
    {
        return $this->get('sw', function (): float {
            if ($this->item()->sw_saisi()) {
                return $this->item()->sw_saisi();
            }
            return $this->repository->sw(
                type_baie: $this->item()->type_baie(),
                type_pose: $this->item()->type_pose(),
                presence_soubassement: $this->item()->presence_soubassement(),
                materiau: $this->item()->materiau(),
                type_vitrage: $this->item()->type_vitrage(),
                type_survitrage: $this->item()->type_survitrage(),
            ) ?? throw new \DomainException("Valeur forfaitaire sw non trouvée");
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            sw: $this->sw(),
        ));
    }
}
