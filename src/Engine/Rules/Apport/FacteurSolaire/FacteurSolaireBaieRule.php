<?php

namespace App\Engine\Rules\Apport\FacteurSolaire;

use App\Engine\Input\Enveloppe\BaieInputRuleIterator;
use App\Engine\Table\BaieTableValeurRepository;

final class FacteurSolaireBaieRule extends BaieInputRuleIterator
{
    public function __construct(
        private BaieTableValeurRepository $repository,
    ) {}

    /**
     * Proportion d’énergie solaire incidente solaire de la baie
     */
    public function sw(): float
    {
        return $this->get('sw', function (): float {
            return $this->sw1() * $this->sw2();
        });
    }

    /**
     * Proportion d’énergie solaire incidente solaire de la baie
     */
    public function sw1(): float
    {
        return $this->get('sw1', function (): float {
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
     * @use FacteurSolaireDoubleFenetreRule
     */
    public function sw2(): float
    {
        return $this->get('sw2', function (): float {
            return $this->item()->double_fenetre()?->sw() ?? 1;
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
