<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Lnc\Baie\Baie;
use App\Domain\Enveloppe\Lnc\{Lnc, TypeLnc};
use App\Domain\Enveloppe\Lnc\Paroi\Paroi;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Apport\SurfaceSudEquivalente\CoefficientTransparenceEtsRule;
use App\Engine\Rules\Apport\SurfaceSudEquivalente\SurfaceSudEquivalenteEtsRule;
use App\Engine\Rules\Deperdition\DeperditionLncRule;

final class LncInput extends Input
{
    /**
     * @var LncParoiInput[]
     */
    public readonly array $parois;
    /**
     * @var LncBaieInput[]
     */
    public readonly array $baies;

    public function __construct(
        public readonly Engine $context,
        public readonly Lnc $entity,
    ) {
        $this->parois = $entity->parois()
            ->map(fn(Paroi $item) => new LncParoiInput($context, $item))
            ->values();
        $this->baies = $entity->baies()
            ->map(fn(Baie $item) => new LncBaieInput($context, $item))
            ->values();
    }

    public function type(): TypeLnc
    {
        return $this->entity->type();
    }

    /**
     * @return BaieInput[]
     */
    public function baies_mitoyennes(): array
    {
        return array_filter(
            $this->context->data()->enveloppe->baies,
            fn(BaieInput $item) => $item->local_non_chauffe()?->entity->id()->compare($this->entity->id()),
        );
    }

    public function b(): ?float
    {
        /** @var DeperditionLncRule $rule */
        $rule = $this->requireIterator(DeperditionLncRule::class, $this);
        return $rule->b();
    }

    public function bver(bool $isolation): ?float
    {
        /** @var DeperditionLncRule $rule */
        $rule = $this->requireIterator(DeperditionLncRule::class, $this);
        return $rule->bver($isolation);
    }

    public function t(): ?float
    {
        /** @var CoefficientTransparenceEtsRule $rule */
        $rule = $this->requireIterator(CoefficientTransparenceEtsRule::class, $this);
        return $rule->t();
    }

    public function ssind(Mois $mois): ?float
    {
        /** @var SurfaceSudEquivalenteEtsRule $rule */
        $rule = $this->requireIterator(SurfaceSudEquivalenteEtsRule::class, $this);
        return $$rule->ssind_j($mois);
    }
}
