<?php

namespace App\Engine\Rules\Refroidissement;

use App\Engine\Input\Refroidissement\GenerateurInputRuleIterator;
use App\Engine\Table\RefroidissementTableValeurRepository;

final class PerformanceGenerateurRule extends GenerateurInputRuleIterator
{
    public function __construct(
        private RefroidissementTableValeurRepository $repository
    ) {}

    /**
     * Coefficient d'efficience énergétique
     */
    public function eer(): float
    {
        return $this->get('eer', function () {
            if ($this->item()->seer_saisi()) {
                return $this->item()->seer_saisi() * 0.95;
            }
            return $this->repository->eer(
                zone_climatique: $this->data()->batiment->zone_climatique(),
                annee_installation_generateur: $this->item()->annee_installation(),
            ) ?? throw new \DomainException('Valeur forfaitaire EER non trouvé');
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            eer: $this->eer(),
        ));
    }
}
