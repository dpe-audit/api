<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Paroi\{Mitoyennete, Paroi, TypeParoi};
use App\Engine\RuleIterator;
use App\Engine\Table\ParoiTableValeurRepository;

/**
 * @template T of Paroi
 * 
 * @extends RuleIterator<T>
 */
abstract class DeperditionParoiRule extends RuleIterator
{
    private ParoiTableValeurRepository $repository;

    // * Données d'entrée

    public function type_paroi(): TypeParoi
    {
        return $this->item()->type_paroi();
    }

    public function surface(): float
    {
        return $this->item()->surface();
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->item()->mitoyennete();
    }

    /**
     * Etat d'isolation de la paroi
     */
    abstract public function isolation(): bool;

    // * Données calculées

    /**
     * Coefficient de transmission thermique exprimé en W/m².K
     */
    abstract public function u(): float;

    /**
     * Déperditions thermiques exprimées en W/K
     */
    public function dp(): float
    {
        return $this->get('dp', function (): float {
            return $this->sdep() * $this->u() * $this->b();
        });
    }

    /**
     * Surface déperditive en m²
     */
    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            return $this->mitoyennete() !== Mitoyennete::LOCAL_RESIDENTIEL
                ? $this->surface()
                : 0;
        });
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): float
    {
        return $this->get('b', function (): float {
            return $this->mitoyennete() === Mitoyennete::LOCAL_NON_CHAUFFE
                ? $this->b_lnc() ?? throw new \DomainException('Valeur b non calculée')
                : $this->repository->b($this->mitoyennete()) ?? throw new \DomainException('Valeur forfaitaire b non trouvée');
        });
    }

    /**
     * Coefficient de réduction des déperditions thermiques du local non chauffé
     */
    public function b_lnc(): ?float
    {
        return $this->get('b_lnc', function (): ?float {
            if (null === $this->item()->local_non_chauffe()) {
                return null;
            }
            $rule = $this->requireIterator(DeperditionLncRule::class, $this->item()->local_non_chauffe());
            return $rule->b() || $rule->bver($this->isolation());
        });
    }
}
