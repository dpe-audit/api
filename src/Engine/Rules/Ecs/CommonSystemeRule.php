<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\{Mois, Scenario};
use App\Domain\Ecs\Generateur\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Domain\Ecs\Systeme\Reseau\{BouclageReseau, IsolationReseau};
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

    public function isolation_reseau(): IsolationReseau
    {
        return $this->item()->reseau()->isolation ?? IsolationReseau::NON_ISOLE;
    }

    public function niveaux_desservis(): int
    {
        return $this->item()->reseau()->niveaux_desservis;
    }

    public function volume_stockage(): float
    {
        return $this->item()->stockage()->volume ?? 0;
    }

    public function volume_stockage_integre(): float
    {
        return $this->item()->generateur()->signaletique()->volume_stockage ?? 0;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->item()->generateur()->position()->position_volume_chauffe ?? false;
    }

    public function position_chauffe_eau(): PositionChauffeEau
    {
        return $this->item()->generateur()->position()->position_chauffe_eau ?? PositionChauffeEau::CHAUFFE_EAU_VERTICAL;
    }

    public function label_generateur(): ?LabelGenerateur
    {
        return $this->item()->generateur()->signaletique()->label;
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

    public function becs(Scenario $scenario, ?Mois $mois = null): float
    {
        return $this->require(PerformanceEcsRule::class)->becs($scenario, $mois);
    }

    public function cop(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->cop();
    }

    public function pn(): float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->pn();
    }

    public function qp0(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->qp0();
    }

    public function rpn(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->rpn();
    }

    public function pveilleuse(): ?float
    {
        return $this->requireIterator(PerformanceGenerateurRule::class, $this->item()->generateur())->pveilleuse();
    }
}
