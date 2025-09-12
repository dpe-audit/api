<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Engine\Input\Enveloppe\{LncBaieInput, LncInputRuleIterator};
use App\Engine\Table\LncTableValeurRepository;

final class DeperditionLncRule extends LncInputRuleIterator
{
    public function __construct(
        private LncTableValeurRepository $repository,
    ) {}

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): ?float
    {
        return $this->get('b', function (): ?float {
            if ($this->item()->type() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
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
     * Coefficient surfacique équivalent exprimé en W/(m2.K)
     */
    public function uvue(): ?float
    {
        return $this->get('uvue', function (): ?float {
            if ($this->item()->type() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            return $this->repository->uvue(type_lnc: $this->item()->type())
                ?? throw new \DomainException("Valeur forfaitaire Uvue non trouvée");
        });
    }

    /**
     * Somme des surfaces des parois donannt sur l'extérieur
     */
    public function aue(?bool $isolation = null): float
    {
        $key = null === $isolation ? 'aue' : "aue::{$isolation}";

        return $this->get($key, function () use ($isolation) {
            $aue = 0;
            foreach ($this->item()->parois as $item) {
                if (null === $isolation || $isolation === $item->isolation()) {
                    $aue += $item->aue();
                }
            }
            foreach ($this->item()->baies as $item) {
                if (null === $isolation || $isolation === $item->isolation()) {
                    $aue += $item->aue();
                }
            }
            return $aue;
        });
    }

    /**
     * Somme des surfaces des parois donnant sur l'espace chauffé
     */
    public function aiu(?bool $isolation = null): float
    {
        $key = null === $isolation ? 'aiu' : "aiu::{$isolation}";

        return $this->get($key, function () use ($isolation) {
            $aiu = 0;
            foreach ($this->item()->parois as $item) {
                if (null === $isolation || $isolation === $item->isolation()) {
                    $aiu += $item->aiu();
                }
            }
            foreach ($this->item()->baies as $item) {
                if (null === $isolation || $isolation === $item->isolation()) {
                    $aiu += $item->aiu();
                }
            }
            return $aiu;
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
            if ($this->item()->type() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
                return null;
            }
            $orientations = $this->orientations();

            if (empty($orientations)) {
                throw new \DomainException("Aucune orientation majeure pour l'espace tampon solarisé");
            }
            $bver = array_map(function (Orientation $orientation) use ($isolation): float {
                return $this->repository->bver(
                    zone_climatique: $this->data()->batiment->zone_climatique(),
                    orientation: $orientation,
                    isolation_paroi: $isolation
                ) ?? throw new \DomainException("Valeur forfaitaire bver non trouvée");
            }, $orientations);

            return array_sum($bver) / count($orientations);
        });
    }

    /**
     * Orientations majoritaires de l'espace tampon solarisé
     * 
     * @return Orientation[]
     */
    public function orientations(): array
    {
        return $this->get('orientations', function (): array {
            /** @var array<string, float> */
            $orientations = [];

            foreach (Orientation::cases() as $orientation) {
                $orientations[$orientation->value] = array_reduce(
                    array_filter($this->item()->baies, fn(LncBaieInput $item) => $item->orientation() === $orientation),
                    fn(float $carry, LncBaieInput $item): float => $carry += $item->surface(),
                    0
                );
            }
            $max = max($orientations);
            $keys = array_keys($orientations, $max);
            return array_map(fn($key) => Orientation::from($key), $keys);
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            b: $this->b(),
            uvue: $this->uvue(),
            aue: $this->aue(),
            aiu: $this->aiu(),
        ));
    }
}
