<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Ventilation\Generateur\{TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\Installation\{Installation, TypeVentilation};
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\WithBatimentRule;

/**
 * @extends RuleIterator<Installation>
 */
abstract class CommonInstallationRule extends RuleIterator
{
    use WithBatimentRule;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->ventilation->installations()->values();
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
        return $this->input()->ventilation->installations()->surface();
    }

    public function type_installation(): TypeVentilation
    {
        return $this->item()->type();
    }

    public function type_generateur(): ?TypeGenerateur
    {
        return $this->item()->generateur()?->type();
    }

    public function type_vmc(): ?TypeVmc
    {
        return $this->item()->generateur()
            ? $this->item()->generateur()->type_vmc() ?? TypeVmc::AUTOREGLABLE
            : null;
    }

    public function generateur_collectif(): ?bool
    {
        return $this->item()->generateur()?->generateur_collectif();
    }

    public function presence_echangeur_thermique(): ?bool
    {
        return $this->item()->generateur()
            ? $this->item()->generateur()->presence_echangeur_thermique() ?? false
            : null;
    }

    public function annee_installation(): ?int
    {
        return $this->item()->generateur()
            ? $this->item()->generateur()->annee_installation() ?? $this->input()->batiment->annee_construction
            : null;
    }
}
