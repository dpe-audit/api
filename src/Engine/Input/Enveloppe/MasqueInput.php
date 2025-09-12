<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\ConfigurationMasque;
use App\Domain\Enveloppe\Masque\Masque;
use App\Domain\Enveloppe\Masque\SecteurMasque;
use App\Domain\Enveloppe\Masque\TypeMasque;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Apport\FacteurEnsoleillement\FacteurEnsoleillementMasqueRule;

final class MasqueInput extends Input
{
    public function __construct(
        public readonly Engine $context,
        public readonly Masque $entity,
    ) {}

    public function type(): TypeMasque
    {
        return $this->entity->type();
    }

    public function configuration(): ConfigurationMasque
    {
        return $this->entity->configuration();
    }

    public function orientation(): ?Orientation
    {
        return $this->entity->orientation();
    }

    public function secteur(): ?SecteurMasque
    {
        return $this->entity->secteur();
    }

    public function hauteur(): ?float
    {
        return $this->entity->hauteur();
    }

    public function profondeur(): ?float
    {
        return $this->entity->profondeur();
    }

    public function fe1(): ?float
    {
        /** @var FacteurEnsoleillementMasqueRule $rule */
        $rule = $this->requireIterator(FacteurEnsoleillementMasqueRule::class, $this);
        return $rule->fe1();
    }

    public function fe2(): ?float
    {
        /** @var FacteurEnsoleillementMasqueRule $rule */
        $rule = $this->requireIterator(FacteurEnsoleillementMasqueRule::class, $this);
        return $rule->fe2();
    }

    public function omb(): ?float
    {
        /** @var FacteurEnsoleillementMasqueRule $rule */
        $rule = $this->requireIterator(FacteurEnsoleillementMasqueRule::class, $this);
        return $rule->omb();
    }
}
