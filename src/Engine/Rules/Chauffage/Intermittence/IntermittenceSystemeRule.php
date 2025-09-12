<?php

namespace App\Engine\Rules\Chauffage\Intermittence;

use App\Domain\Chauffage\Emetteur\TypeEmission;
use App\Domain\Chauffage\Installation\Regulation\TypeIntermittence;
use App\Engine\Input\Chauffage\SystemeInputRuleIterator;
use App\Engine\Tables\ChauffageTableValeurRepository;

final class IntermittenceSystemeRule extends SystemeInputRuleIterator
{
    public function __construct(
        private ChauffageTableValeurRepository $repository
    ) {}

    /**
     * Facteur d'intermittence
     */
    public function int(): float
    {
        return $this->get('int', function (): float {
            $g = $this->data()->enveloppe->gv() / $this->data()->batiment->volume_habitable();
            $i0 = $this->i0();
            return $i0 / (1 + 0.1 * ($g - 1));
        });
    }

    /**
     * Coefficient d'intermittence
     */
    public function i0(): float
    {
        return $this->get("i0", function (): float {
            if (count($this->item()->emetteurs())) {
                $values = [];
                foreach ($this->item()->emetteurs() as $emetteur) {
                    $values[] = $this->repository->i0(
                        type_batiment: $this->data()->batiment->type_batiment(),
                        type_emission: $emetteur->type_emission(),
                        type_intermittence: $this->type_intermittence(),
                        regulation_terminale: $this->item()->installation()->regulation_terminale()->presence_regulation,
                        inertie_lourde: $this->data()->enveloppe->inertie()->lourde(),
                        comptage_individuel: $this->item()->installation()->comptage_individuel(),
                        chauffage_collectif: $this->item()->systeme_collectif(),
                        chauffage_central: true,
                    ) ?? throw new \DomainException('Valeur forfaitaire I0 non trouvée');
                }
                return array_sum($values) / count($values);
            }
            return $this->repository->i0(
                type_batiment: $this->data()->batiment->type_batiment(),
                type_emission: TypeEmission::from_type_generateur($this->item()->generateur()->type()),
                type_intermittence: $this->type_intermittence(),
                regulation_terminale: $this->item()->installation()->regulation_terminale()->presence_regulation,
                inertie_lourde: $this->data()->enveloppe->inertie()->lourde(),
                comptage_individuel: $this->item()->installation()->comptage_individuel(),
                chauffage_collectif: $this->item()->generateur()->generateur_collectif(),
                chauffage_central: false,
            ) ?? throw new \DomainException('Valeur forfaitaire I0 non trouvée');
        });
    }

    /**
     * Type d'intermittence
     */
    public function type_intermittence(): TypeIntermittence
    {
        return $this->get('type_intermittence', function (): TypeIntermittence {
            return TypeIntermittence::determine(
                regulation_centrale: $this->item()->installation()->regulation_centrale(),
                regulation_terminale: $this->item()->installation()->regulation_terminale(),
                chauffage_collectif: $this->item()->systeme_collectif(),
            );
        });
    }
}
