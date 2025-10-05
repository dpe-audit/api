<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\Paroi\{Mitoyennete, TypeParoi};
use App\Domain\Enveloppe\Porte\{Isolation, Materiau, Porte};
use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;
use App\Engine\Engine;
use App\Engine\Rules\Deperdition\DeperditionPorteRule;

final class PorteInput extends ParoiInput
{
    public function __construct(
        public readonly Engine $context,
        public readonly Porte $entity,
    ) {}

    public function type_paroi(): TypeParoi
    {
        return $this->entity->type_paroi();
    }

    public function surface(): float
    {
        return $this->entity->position()->surface;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->entity->position()->mitoyennete;
    }

    public function presence_sas(): bool
    {
        return $this->entity->position()->presence_sas;
    }

    public function isolation(): bool
    {
        return $this->entity->isolation() === Isolation::ISOLE;
    }

    public function materiau(): Materiau
    {
        return $this->entity->materiau() ?? Materiau::PVC;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return $this->entity->vitrage()->surface
            ? $this->entity->vitrage()->type ?? TypeVitrage::SIMPLE_VITRAGE
            : null;
    }

    public function taux_vitrage(): float
    {
        return $this->entity->vitrage()->surface
            ? $this->entity->vitrage()->surface / $this->entity->position()->surface * 100
            : 0;
    }

    public function presence_joint(): bool
    {
        return $this->entity->menuiserie()?->presence_joint ?? false;
    }

    public function u_saisi(): ?float
    {
        return $this->entity->u();
    }

    public function local_non_chauffe(): ?LncInput
    {
        return array_find(
            $this->context->data()->enveloppe->locaux_non_chauffes,
            fn(LncInput $item) => $item->entity === $this->entity->position()->local_non_chauffe
        );
    }

    public function sdep(): float
    {
        /** @var DeperditionPorteRule $rule */
        $rule = $this->requireIterator(DeperditionPorteRule::class, $this);
        return $rule->sdep();
    }

    public function dp(): float
    {
        /** @var DeperditionPorteRule $rule */
        $rule = $this->requireIterator(DeperditionPorteRule::class, $this);
        return $rule->dp();
    }
}
