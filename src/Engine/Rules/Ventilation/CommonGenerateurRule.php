<?php

namespace App\Engine\Rules\Ventilation;

use App\Domain\Ventilation\Generateur\{Generateur, TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\Installation\TypeVentilation;
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\WithBatiment;

/**
 * @extends RuleIterator<Generateur>
 */
abstract class CommonGenerateurRule extends RuleIterator
{
    use WithBatiment;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->ventilation->generateurs()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function type_ventilation(): TypeVentilation
    {
        return TypeVentilation::VENTILATION_MECANIQUE;
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->type();
    }

    public function type_vmc(): TypeVmc
    {
        return $this->item()->type_vmc() ?? TypeVmc::AUTOREGLABLE;
    }

    public function generateur_collectif(): bool
    {
        return $this->item()->generateur_collectif();
    }

    public function presence_echangeur_thermique(): bool
    {
        return $this->item()->presence_echangeur_thermique() ?? false;
    }

    public function annee_installation(): int
    {
        return $this->item()->annee_installation() ?? $this->input()->batiment->annee_construction;
    }

    public function surface(): float
    {
        return $this->input()->ventilation->installations()->with_generateur($this->item()->id())->surface();
    }

    /**
     * @return float[]
     */
    public function rdim_installations(): array
    {
        return $this->input()->ventilation->installations()
            ->with_generateur($this->item()->id())
            ->map(fn($item) => $this->requireIterator(PerformanceInstallationRule::class, $item)->rdim())
            ->values();
    }
}
