<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\Lnc\Paroi\{Isolation, Mitoyennete, Paroi};
use App\Engine\{Engine, Input};
use App\Engine\Rules\Deperdition\DeperditionLncParoiRule;

final class LncParoiInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Paroi $entity,
    ) {}

    public function surface(): float
    {
        return $this->entity->position()->surface;
    }

    public function isolation(): bool
    {
        return $this->entity->isolation() === Isolation::ISOLE ?? false;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->entity->position()->mitoyennete;
    }

    public function aue(): float
    {
        /** @var DeperditionLncParoiRule $rule */
        $rule = $this->requireIterator(DeperditionLncParoiRule::class, $this);
        return $rule->aue();
    }

    public function aiu(): float
    {
        /** @var DeperditionLncParoiRule $rule */
        $rule = $this->requireIterator(DeperditionLncParoiRule::class, $this);
        return $rule->aiu();
    }
}
