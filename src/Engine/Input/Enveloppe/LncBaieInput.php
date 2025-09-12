<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Lnc\Baie\{Baie, Materiau, Mitoyennete, TypeVitrage};
use App\Engine\{Engine, Input};
use App\Engine\Rules\Apport\SurfaceSudEquivalente\CoefficientTransparenceEtsBaieRule;
use App\Engine\Rules\Apport\SurfaceSudEquivalente\SurfaceSudEquivalenteEtsBaieRule;
use App\Engine\Rules\Deperdition\DeperditionLncBaieRule;

final class LncBaieInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Baie $entity,
    ) {}

    public function local_non_chauffe(): LncInput
    {
        return array_find(
            $this->context->data()->enveloppe->locaux_non_chauffes,
            fn(LncInput $item) => $item->entity === $this->entity->local_non_chauffe(),
        );
    }

    public function surface(): float
    {
        return $this->entity->position()->surface;
    }

    public function orientation(): ?Orientation
    {
        return $this->entity->position()->orientation
            ? Orientation::from_azimut($this->entity->position()->orientation)
            : null;
    }

    public function inclinaison(): float
    {
        return $this->entity->position()->inclinaison;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->entity->position()->mitoyennete;
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->entity->type_vitrage();
    }

    public function materiau(): Materiau
    {
        return $this->entity->materiau() ?? Materiau::PVC;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->entity->presence_rupteur_pont_thermique() ?? false;
    }

    public function isolation(): bool
    {
        return $this->type_vitrage()->isolation();
    }

    public function aue(): float
    {
        /** @var DeperditionLncBaieRule $rule */
        $rule = $this->requireIterator(DeperditionLncBaieRule::class, $this);
        return $rule->aue();
    }

    public function aiu(): float
    {
        /** @var DeperditionLncBaieRule $rule */
        $rule = $this->requireIterator(DeperditionLncBaieRule::class, $this);
        return $rule->aiu();
    }

    public function t(): float
    {
        /** @var CoefficientTransparenceEtsBaieRule $rule */
        $rule = $this->requireIterator(CoefficientTransparenceEtsBaieRule::class, $this);
        return $rule->t();
    }

    public function sst(?Mois $mois = null): float
    {
        /** @var SurfaceSudEquivalenteEtsBaieRule $rule */
        $rule = $this->requireIterator(SurfaceSudEquivalenteEtsBaieRule::class, $this);
        return $mois ? $rule->sst_j($mois) : $rule->sst();
    }
}
