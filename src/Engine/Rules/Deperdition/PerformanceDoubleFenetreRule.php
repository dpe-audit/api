<?php

namespace App\Engine\Rules\Deperdition;

use App\Engine\Input\Enveloppe\DoubleFenetreInputRuleIterator;
use App\Engine\Table\DoubleFenetreTableValeurRepository;

final class PerformanceDoubleFenetreRule extends DoubleFenetreInputRuleIterator
{
    public function __construct(
        private DoubleFenetreTableValeurRepository $repository
    ) {}

    /**
     * Coefficient de transmission thermique du vitrage exprimé en W/m².K
     */
    public function ug(): float
    {
        return $this->get('ug', function (): float {
            return $this->item()->ug_saisi() ?? $this->repository->ug(
                type_baie: $this->item()->type_baie(),
                type_vitrage: $this->item()->type_vitrage(),
                nature_gaz_lame: $this->item()->nature_lame(),
                inclinaison_vitrage: $this->item()->inclinaison(),
                epaisseur_lame_air: $this->item()->epaisseur_lame(),
            ) ?? throw new \DomainException('Valeur forfaitaire ug non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie exprimé en W/m².K
     */
    public function uw(): float
    {
        return $this->get('uw', function (): float {
            return $this->item()->uw_saisi() ?? $this->repository->uw(
                ug: $this->ug(),
                type_baie: $this->item()->type_baie(),
                presence_soubassement: $this->item()->presence_soubassement(),
                materiau: $this->item()->materiau(),
                presence_rupteur_pont_thermique: $this->item()->presence_rupteur_pont_thermique(),
            ) ?? throw new \DomainException('Valeur forfaitaire uw non trouvée');
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            ug: $this->ug(),
            uw: $this->uw(),
        ));
    }
}
