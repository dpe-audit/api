<?php

namespace App\Engine\Rules\Chauffage;

use App\Domain\Chauffage\Installation\Installation;
use App\Domain\Chauffage\TypeChauffage;
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\{WithBatiment, WithBatimentRule};

/**
 * @extends RuleIterator<Installation>
 */
abstract class CommonInstallationRule extends RuleIterator
{
    use WithBatiment, WithBatimentRule;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->chauffage->installations()->values();
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
        return $this->input()->chauffage->installations()->surface();
    }

    public function solaire_thermique(): bool
    {
        return $this->item()->solaire_thermique() !== null;
    }

    public function fch_saisi(): ?float
    {
        return $this->item()->solaire_thermique()?->fch;
    }

    public function installation_individuelle(): bool
    {
        return false === $this->item()->systemes()->has_generateur_collectif();
    }

    public function installation_collective(): bool
    {
        return $this->item()->systemes()->has_generateur_collectif();
    }

    public function systemes_chauffage_central(bool $systeme_collectif): int
    {
        return $this->item()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)
            ->count();
    }

    public function presence_pac(bool $systeme_collectif): bool
    {
        return $this->item()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->has_pac();
    }

    public function presence_chaudiere_bois(bool $systeme_collectif): bool
    {
        return $this->item()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->has_chaudiere_bois();
    }

    public function presence_chaudiere(bool $systeme_collectif): bool
    {
        return $this->item()->systemes()
            ->filter_by_systeme_collectif($systeme_collectif)
            ->with_type(TypeChauffage::CHAUFFAGE_DIVISE)
            ->has_chaudiere();
    }
}
