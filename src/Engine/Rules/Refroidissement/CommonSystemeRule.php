<?php

namespace App\Engine\Rules\Refroidissement;

use App\Domain\Refroidissement\Generateur\EnergieGenerateur;
use App\Domain\Refroidissement\Systeme\Systeme;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Systeme>
 */
abstract class CommonSystemeRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->refroidissement->systemes()->values();
    }

    public function nombre_systemes(): int
    {
        return $this->item()->installation()->systemes()->count();
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->generateur()->energie();
    }

    public function contenu_co2_reseau_froid(): ?float
    {
        return $this->item()->generateur()->reseau_froid()?->contenu_co2();
    }

    public function bfr(): float
    {
        return $this->require(PerformanceRefroidissementRule::class)->bfr();
    }

    public function rdim_installation(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->rdim();
    }

    public function eer(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->eer();
    }
}
