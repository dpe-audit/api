<?php

namespace App\Engine\Rules\Apport\FacteurEnsoleillement;

use App\Engine\Input\Enveloppe\MasqueInputRuleIterator;
use App\Engine\Table\MasqueTableValeurRepository;

final class FacteurEnsoleillementMasqueRule extends MasqueInputRuleIterator
{
    public function __construct(
        private MasqueTableValeurRepository $repository
    ) {}

    /**
     * Facteur d'ensoleillement du masque proche
     */
    public function fe1(): float
    {
        return $this->get('fe1', function (): float {
            return $this->repository->fe1(
                configuration_masque: $this->item()->configuration(),
                orientation_facade: $this->item()->orientation(),
                avancee_masque: $this->item()->profondeur(),
            ) ?? throw new \DomainException('Valeur forfaitaire FE1 non trouvée');
        });
    }

    /**
     * Facteur d'ensoleillement du masque lointain homogène
     */
    public function fe2(): float
    {
        return $this->get('fe2', function (): float {
            return $this->repository->fe2(
                configuration_masque: $this->item()->configuration(),
                orientation_facade: $this->item()->orientation(),
                hauteur_masque_alpha: $this->item()->hauteur(),
            ) ?? throw new \DomainException('Valeur forfaitaire FE2 non trouvée');
        });
    }

    /**
     * Ombrage du masque lointain non homogène
     */
    public function omb(): float
    {
        return $this->get('omb', function (): float {
            return $this->repository->omb(
                configuration_masque: $this->item()->configuration(),
                orientation_facade: $this->item()->orientation(),
                secteur: $this->item()->secteur(),
                hauteur_masque_alpha: $this->item()->hauteur(),
            ) ?? throw new \DomainException('Valeur forfaitaire OMB non trouvée');
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            fe1: $this->fe1(),
            fe2: $this->fe2(),
            omb: $this->omb(),
        ));
    }
}
