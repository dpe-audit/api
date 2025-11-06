<?php

namespace App\Engine\Rules\Ecs;

use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\Generateur\{EnergieGenerateur, Generateur, TypeGenerateur};
use App\Domain\Ecs\Generateur\Position\PositionChauffeEau;
use App\Domain\Ecs\Generateur\Signaletique\LabelGenerateur;
use App\Engine\Context;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\EcsTableValeurRepository;

abstract class PerformanceGenerateurRule extends DimensionnementGenerateurRule
{
    use WithBatimentRule;

    public function __construct(
        protected EcsTableValeurRepository $repository,
    ) {}

    abstract public static function supports(Generateur $entity): bool;

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->ecs->generateurs()
            ->filter(fn(Generateur $item) => static::supports($item))
            ->values();
    }

    // * Données d'entrée

    public function type(): TypeGenerateur
    {
        return $this->item()->type() ?? TypeGenerateur::CHAUDIERE;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->item()->energie() ?? EnergieGenerateur::FIOUL;
    }

    public function contenu_co2_reseau_chaleur(): ?float
    {
        return $this->item()->position()->reseau_chaleur?->contenu_co2();
    }

    public function position_chauff_eau(): PositionChauffeEau
    {
        return $this->item()->position()->position_chauff_eau ?? PositionChauffeEau::CHAUFFE_EAU_VERTICAL;
    }

    public function annee_installation(): int
    {
        return $this->item()->annee_installation() ?? $this->input()->batiment->annee_construction;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->item()->position()->position_volume_chauffe;
    }

    public function label(): ?LabelGenerateur
    {
        return $this->item()->signaletique()->label;
    }


    // * Données de sortie

    /**
     * Coefficient de performance énergétique
     */
    public function cop(): ?float
    {
        return null;
    }

    /**
     * Rendement à pleine charge exprimée en %
     */
    public function rpn(): ?float
    {
        return null;
    }

    /**
     * Pertes à l'arrêt du générateur exprimée en W
     */
    public function qp0(): ?float
    {
        return null;
    }

    /**
     * Puissance de la veilleuse exprimée en W
     */
    public function pveilleuse(): ?float
    {
        return null;
    }

    /**
     * Pertes de génération  en Wh
     */
    public function pertes_generation(?Mois $mois = null): float
    {
        return 0;
    }

    /**
     * Pertes de génération récupérables exprimées en Wh
     */
    public function pertes_generation_recuperables(?Mois $mois = null): float
    {
        return 0;
    }

    /**
     * Pertes de stockage intégré en Wh
     */
    public function pertes_stockage(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage::{$mois->value}" : "pertes_stockage";
        return $this->get($key, function () use ($mois): float {
            if (0 === $vs = $this->volume_stockage()) {
                return 0;
            }
            if (false === \in_array($this->position_chauff_eau(), [
                PositionChauffeEau::CHAUFFE_EAU_HORIZONTAL,
                PositionChauffeEau::CHAUFFE_EAU_VERTICAL,
            ])) {
                return (67662 * \pow($vs, 0.55)) / 12;
            }
            $cr = $this->repository->cr(
                type_generateur: $this->type(),
                label_generateur: $this->label(),
                volume_stockage: $vs,
            ) ?? throw new \DomainException("Valeur forfaitaire Cr non trouvée");

            $value = (8592 * (45 / 24) * $vs * $cr);
            return $mois ? $value / 12 : $value;
        });
    }

    /**
     * Pertes de stockage intégré récupérables en Wh
     */
    public function pertes_stockage_recuperables(?Mois $mois = null): float
    {
        $key = $mois ? "pertes_stockage_recuperables::{$mois->value}" : "pertes_stockage_recuperables";
        return $this->get($key, function () use ($mois): float {
            if (null === $mois) {
                return Mois::reduce(fn(Mois $item): float => $this->pertes_stockage_recuperables($item));
            }
            return $this->position_volume_chauffe()
                ? 0.48 * $this->nref($mois) * ($this->pertes_stockage() / 8760)
                : 0;
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                rdim: $rule->rdim(),
                pn: $rule->pn(),
                pdim: $rule->pdim(),
                pecs: $rule->pecs(),
                cop: $rule->cop(),
                rpn: $rule->rpn(),
                qp0: $rule->qp0(),
                pveilleuse: $rule->pveilleuse(),
                pertes_generation: $rule->pertes_generation(),
                pertes_generation_recuperables: $rule->pertes_generation_recuperables(),
                pertes_stockage: $rule->pertes_stockage(),
                pertes_stockage_recuperables: $rule->pertes_stockage_recuperables(),
            ));
        }
    }
}
