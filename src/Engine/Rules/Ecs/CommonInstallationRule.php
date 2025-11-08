<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Ecs\Installation\Installation;
use App\Domain\Ecs\Installation\Solaire\Usage as UsageSolaire;
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\WithBatiment;

/**
 * @extends RuleIterator<Installation>
 */
abstract class CommonInstallationRule extends RuleIterator
{
    use WithBatiment;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->ecs->installations()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function surface(): float
    {
        return $this->item()->surface();
    }

    public function surface_totale(): float
    {
        return $this->input()->ecs->installations()->surface();
    }

    public function fecs_saisi(): ?float
    {
        return $this->item()->solaire_thermique()->fecs;
    }

    public function solaire_thermique(): bool
    {
        return null !== $this->item()->solaire_thermique();
    }

    public function usage_solaire_thermique(): ?UsageSolaire
    {
        return $this->item()->solaire_thermique()->usage;
    }

    public function annee_installation_solaire_thermique(): int
    {
        return $this->item()->solaire_thermique()->annee_installation ?? $this->input()->batiment->annee_construction;
    }
}
