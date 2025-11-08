<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\Systeme\Reseau\BouclageReseau;
use App\Domain\Ecs\Systeme\Systeme;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Systeme>
 */
abstract class CommonSystemeRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->ecs->systemes()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    public function nombre_systemes(): int
    {
        return $this->item()->installation()->systemes()->count();
    }

    public function surface(): float
    {
        return $this->item()->installation()->surface();
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->item()->generateur()->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return $this->item()->generateur()->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function generateur_collectif(): bool
    {
        return $this->item()->generateur()->position()->generateur_collectif;
    }

    public function generateur_multi_batiment(): bool
    {
        return $this->item()->generateur()->position()->generateur_multi_batiment;
    }

    public function presence_ventouse(): bool
    {
        return $this->item()->generateur()->signaletique()->presence_ventouse ?? false;
    }

    public function bouclage_reseau(): BouclageReseau
    {
        return $this->item()->reseau()->bouclage ?? BouclageReseau::RESEAU_BOUCLE;
    }

    public function niveaux_desservis(): int
    {
        return $this->item()->reseau()->niveaux_desservis;
    }

    public function volume_stockage(): float
    {
        return $this->item()->stockage()->volume ?? 0;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->item()->generateur()->position()->position_volume_chauffe ?? false;
    }

    public function position_volume_chauffe_stockage(): bool
    {
        return $this->item()->stockage()->position_volume_chauffe ?? false;
    }

    public function alimentation_contigue(): bool
    {
        return $this->item()->reseau()->alimentation_contigue;
    }

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->item()->generateur()->position()->reseau_chaleur?->contenu_co2();
    }

    public function rdim_installation(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->rdim();
    }

    public function fecs(): float
    {
        return $this->requireIterator(PerformanceInstallationRule::class, $this->item()->installation())->fecs();
    }

    public function becs(?Mois $mois = null): float
    {
        return $this->require(PerformanceEcsRule::class)->becs($mois);
    }

    public function pn(): float
    {
        $entity = $this->item()->generateur();
        return $this->requireIterator(PerformanceGenerateurRule::class, $entity)->pn();
    }
}
