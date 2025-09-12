<?php

namespace App\Domain\Enveloppe\ConfortEte;

final class ConfortEte
{
    public function __construct(
        public readonly ?Performance $performance,
        public readonly ?bool $inertie_lourde,
        public readonly ?bool $isolation_plancher_haut,
        public readonly ?bool $presence_protection_solaire,
        public readonly ?bool $logement_traversant,
        public readonly ?bool $presence_brasseur_air,
    ) {}

    public static function create(
        ?Performance $performance = null,
        ?bool $inertie_lourde = null,
        ?bool $isolation_plancher_haut = null,
        ?bool $presence_protection_solaire = null,
        ?bool $logement_traversant = null,
        ?bool $presence_brasseur_air = null,
    ): self {
        return new self(
            performance: $performance,
            inertie_lourde: $inertie_lourde,
            isolation_plancher_haut: $isolation_plancher_haut,
            presence_protection_solaire: $presence_protection_solaire,
            logement_traversant: $logement_traversant,
            presence_brasseur_air: $presence_brasseur_air,
        );
    }

    public function with(
        ?Performance $performance = null,
        ?bool $inertie_lourde = null,
        ?bool $isolation_plancher_haut = null,
        ?bool $presence_protection_solaire = null,
        ?bool $logement_traversant = null,
        ?bool $presence_brasseur_air = null
    ): self {
        return static::create(
            performance: $performance ?? $this->performance,
            inertie_lourde: $inertie_lourde ?? $this->inertie_lourde,
            isolation_plancher_haut: $isolation_plancher_haut ?? $this->isolation_plancher_haut,
            presence_protection_solaire: $presence_protection_solaire ?? $this->presence_protection_solaire,
            logement_traversant: $logement_traversant ?? $this->logement_traversant,
            presence_brasseur_air: $presence_brasseur_air ?? $this->presence_brasseur_air,
        );
    }
}
