<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Lnc\{Lnc, TypeLnc};
use App\Engine\Context;
use App\Engine\RuleIterator;
use App\Engine\Rules\Batiment\WithBatimentRule;
use App\Engine\Table\LncTableValeurRepository;

/**
 * @extends RuleIterator<Lnc>
 */
final class DeperditionLncRule extends RuleIterator
{
    use WithBatimentRule;

    public function __construct(
        private LncTableValeurRepository $repository,
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->locaux_non_chauffes()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type(): TypeLnc
    {
        return $this->item()->type();
    }

    /**
     * @return Orientation[]
     */
    public function orientations(): array
    {
        return $this->item()->baies()->orientations();
    }

    // * Données intermédiaires

    public function aue(?bool $isolation = null): float
    {
        $aue = 0;

        foreach ($this->item()->parois() as $item) {
            $rule = $this->requireIterator(DeperditionLncParoiRule::class, $item);
            if ($isolation === null || $rule->isolation() === $isolation) {
                $aue += $rule->aue();
            }
        }
        foreach ($this->item()->baies() as $item) {
            $rule = $this->requireIterator(DeperditionLncBaieRule::class, $item);
            if ($isolation === null || $rule->isolation() === $isolation) {
                $aue += $rule->aue();
            }
        }
        return $aue;
    }

    public function aiu(?bool $isolation = null): float
    {
        $aiu = 0;

        foreach ($this->item()->parois() as $item) {
            $rule = $this->requireIterator(DeperditionLncParoiRule::class, $item);
            if ($isolation === null || $rule->isolation() === $isolation) {
                $aiu += $rule->aiu();
            }
        }
        foreach ($this->item()->baies() as $item) {
            $rule = $this->requireIterator(DeperditionLncBaieRule::class, $item);
            if ($isolation === null || $rule->isolation() === $isolation) {
                $aiu += $rule->aiu();
            }
        }
        return $aiu;
    }

    // * Données calculées

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): ?float
    {
        return $this->get('b', function (): ?float {
            if ($this->type() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            return $this->repository->b(
                uvue: $this->uvue(),
                isolation_aiu: $this->isolation_aiu(),
                isolation_aue: $this->isolation_aue(),
                aiu: $this->aiu(),
                aue: $this->aue(),
            ) ?? throw new \DomainException("Valeur forfaitaire b non trouvée");
        });
    }

    /**
     * Coefficient surfacique équivalent en W/(m2.K)
     */
    public function uvue(): ?float
    {
        return $this->get('uvue', function (): ?float {
            if ($this->type() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            return $this->repository->uvue(type_lnc: $this->type())
                ?? throw new \DomainException("Valeur forfaitaire Uvue non trouvée");
        });
    }

    /**
     * Etat d'isolation majoritaire des parois du local non chauffé donnant sur l'extérieur
     */
    public function isolation_aue(): bool
    {
        return $this->get('isolation_aue', function (): float {
            return $this->aue(true) > $this->aue() / 2;
        });
    }

    /**
     * Etat d'isolation majoritaire des parois du local non chauffé donnant sur l'espace chauffé
     */
    public function isolation_aiu(): bool
    {
        return $this->get('isolation_aiu', function () {
            return $this->aiu(true) > $this->aiu() / 2;
        });
    }

    /**
     * Coefficients de réduction des déperditions thermiques
     * 
     * @param bool $isolation Etat d'isolation de la paroi donnant sur l'espace tampon solarisé
     */
    public function bver(bool $isolation): ?float
    {
        $key = "bver::{$isolation}";

        return $this->get($key, function () use ($isolation): ?float {
            if ($this->type() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            $orientations = $this->orientations();

            if (empty($orientations)) {
                throw new \DomainException("Aucune orientation majeure pour l'espace tampon solarisé");
            }
            $bver = array_map(function (Orientation $orientation) use ($isolation): float {
                return $this->repository->bver(
                    zone_climatique: $this->zone_climatique(),
                    orientation: $orientation,
                    isolation_paroi: $isolation
                ) ?? throw new \DomainException("Valeur forfaitaire bver non trouvée");
            }, $orientations);

            return array_sum($bver) / count($orientations);
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
                b: $rule->b(),
                uvue: $rule->uvue(),
                aue: $rule->aue(),
                aiu: $rule->aiu(),
            ));
        }
    }
}
