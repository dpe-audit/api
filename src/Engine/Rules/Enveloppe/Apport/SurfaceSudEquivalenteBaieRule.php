<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Common\Enum\{Mois, Orientation};
use App\Domain\Enveloppe\Baie\{Baie, TypeBaie};
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Position\TypePose;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\Vitrage\TypeVitrage;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Engine\{Context, RuleIterator};
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\{BaieTableValeurRepository, SollicitationsClimatiquesTableValeurRepository};

/**
 * @extends RuleIterator<Baie>
 */
final class SurfaceSudEquivalenteBaieRule extends RuleIterator
{
    use WithBatimentRule;

    public function __construct(
        private BaieTableValeurRepository $repository,
        private SollicitationsClimatiquesTableValeurRepository $ext_repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->baies()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function mitoyennete(): Mitoyennete
    {
        return $this->item()->mitoyennete();
    }

    public function surface(): float
    {
        return $this->item()->surface();
    }

    public function orientation(): ?Orientation
    {
        return $this->item()->position()->orientation(true);
    }

    public function inclinaison(): float
    {
        return $this->item()->position()->inclinaison;
    }

    public function type_lnc(): ?TypeLnc
    {
        return $this->item()->local_non_chauffe()?->type();
    }

    public function sw_saisi(): ?float
    {
        return $this->item()->sw();
    }

    public function type_baie(): TypeBaie
    {
        return $this->item()->type();
    }

    public function type_vitrage(): TypeVitrage
    {
        return $this->item()->vitrage()->type;
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        if (null === $this->item()->survitrage()) {
            return null;
        }
        return $this->item()->survitrage()->type ?? TypeSurvitrage::SURVITRAGE_SIMPLE;
    }

    public function type_pose(): TypePose
    {
        return $this->item()->position()->type_pose ?? TypePose::NU_EXTERIEUR;
    }

    public function presence_soubassement(): bool
    {
        return $this->item()->position()->presence_soubassement ?? false;
    }

    public function materiau(): Materiau
    {
        return $this->item()->menuiserie()?->materiau ?? Materiau::PVC;
    }

    // * Données intermédiaires

    public function sw2(): float
    {
        return $this->get('sw2', function (): float {
            return ($entity = $this->item()->position()->double_fenetre)
                ? $this->requireIterator(SurfaceSudEquivalenteDoubleFenetre::class, $entity)->sw()
                : 1;
        });
    }

    public function fe1(): float
    {
        return $this->get('fe1', function (): float {
            return $this->item()->masques()
                ->map(fn($item) => $this->requireIterator(FacteurEnsoleillementMasqueRule::class, $item)->fe1())
                ->filter(fn($fe) => null !== $fe)
                ->reduce(fn($carry, $fe) => min($carry, $fe), 1);
        });
    }

    public function fe2(): float
    {
        return $this->get('fe2', function (): float {
            $omb = $this->omb();
            $fe2 = $this->item()->masques()
                ->map(fn($item) => $this->requireIterator(FacteurEnsoleillementMasqueRule::class, $item)->fe2())
                ->filter(fn($fe) => null !== $fe)
                ->reduce(fn($carry, $fe) => min($carry, $fe), 1);

            $fe2 = min($fe2, 1 - min($omb, 100) / 100);
            return static::round($fe2);
        });
    }

    public function omb(): float
    {
        return $this->get('omb', function (): float {
            return $this->item()->masques()
                ->map(fn($item) => $this->requireIterator(FacteurEnsoleillementMasqueRule::class, $item)->omb())
                ->filter(fn($fe) => null !== $fe)
                ->reduce(fn($carry, $fe) => min($carry + $fe, 100));
        });
    }

    public function t(): float
    {
        return $this->get('t', function (): float {
            return ($entity = $this->item()->local_non_chauffe())
                ? $this->requireIterator(SurfaceSudEquivalenteEtsRule::class, $entity)->t()
                : 1;
        });
    }

    // * Données calculées

    /**
     * Surface sud équivalente en m²
     */
    public function sse(): float
    {
        return $this->get("sse", function (): float {
            return Mois::reduce(fn(Mois $mois) => $this->sse_j($mois));
        });
    }

    /**
     * Surface sud équivalente pour le mois j en m²
     */
    public function sse_j(Mois $mois): float
    {
        return $this->get("sse::{$mois->value}", function () use ($mois): float {
            if ($this->mitoyennete() !== Mitoyennete::EXTERIEUR) {
                return 0;
            }
            if ($this->type_lnc() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return 0;
            }

            $a = $this->surface();
            $sw = $this->sw();
            $fe = $this->fe();
            $c1 = $this->c1_j($mois);
            $t = $this->t();
            return static::round($a * $sw * $fe * $c1 * $t);
        });
    }

    /**
     * Proportion d’énergie solaire incidente solaire de la baie
     */
    public function sw(): float
    {
        return $this->get('sw', function (): float {
            return $this->sw1() * $this->sw2();
        });
    }

    /**
     * Proportion d’énergie solaire incidente solaire de la baie
     */
    public function sw1(): float
    {
        return $this->get('sw1', function (): float {
            if ($this->sw_saisi()) {
                return $this->sw_saisi();
            }
            return $this->repository->sw(
                type_baie: $this->type_baie(),
                type_pose: $this->type_pose(),
                presence_soubassement: $this->presence_soubassement(),
                materiau: $this->materiau(),
                type_vitrage: $this->type_vitrage(),
                type_survitrage: $this->type_survitrage(),
            ) ?? throw new \DomainException("Valeur forfaitaire sw non trouvée");
        });
    }

    /**
     * Facteur d'ensoleillement de la baie
     */
    public function fe(): float
    {
        return $this->get('fe', function (): float {
            return $this->fe1() * $this->fe2();
        });
    }

    /**
     * Coefficient d'orientation et d'inclinaison de la baie pour le mois j
     */
    public function c1_j(Mois $mois): float
    {
        return $this->get("c1::{$mois->value}", function () use ($mois): float {
            return array_find($this->c1(), fn(array $item) => $item['mois'] === $mois)['c1']
                ?? throw new \DomainException("Valeur forfaitaire C1 non trouvée pour le mois {$mois->value}");
        });
    }

    /**
     * Coefficient d'orientation et d'inclinaison de la baie
     * 
     * @return array{mois: Mois, c1: float}[]
     */
    public function c1(): array
    {
        return $this->get('c1', function (): array {
            return $this->ext_repository->c1(
                zone_climatique: $this->zone_climatique(),
                orientation: $this->orientation(),
                inclinaison: $this->inclinaison(),
            );
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
                sse: $rule->sse(),
                fe: $rule->fe(),
                sw: $rule->sw(),
            ));
        }
    }
}
