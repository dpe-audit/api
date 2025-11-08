<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Paroi\Inertie;
use App\Domain\Enveloppe\Paroi\Isolation\{EtatIsolation, TypeIsolation};
use App\Legacy\Model\{Paroi, ParoiOpaque};

/**
 * @template T of ParoiOpaque
 * 
 * @extends ParoiTransformer<ParoiOpaque>
 */
abstract class ParoiOpaqueTransformer extends ParoiTransformer
{
    /**
     * @var T
     */
    protected Paroi $paroi;

    public function inertie(): ?Inertie
    {
        if (null === $this->paroi->paroi_lourde) {
            return null;
        }
        return $this->paroi->paroi_lourde ? Inertie::LOURDE : Inertie::LEGERE;
    }

    public function epaisseur_isolation(): ?float
    {
        return $this->paroi->epaisseur_isolation ? $this->paroi->epaisseur_isolation * 10 : null;
    }

    public function etat_isolation(): ?EtatIsolation
    {
        return match ($this->paroi->enum_type_isolation_id) {
            2 => EtatIsolation::NON_ISOLE,
            3, 4, 5, 6, 7, 8 => EtatIsolation::ISOLE,
            default => null,
        };
    }

    public function type_isolation(): ?TypeIsolation
    {
        return match ($this->paroi->enum_type_isolation_id) {
            3 => TypeIsolation::ITI,
            4 => TypeIsolation::ITE,
            5 => TypeIsolation::ITR,
            6 => TypeIsolation::ITI_ITE,
            7 => TypeIsolation::ITI_ITR,
            8 => TypeIsolation::ITE_ITR,
            default => null
        };
    }

    public function annee_isolation(): ?int
    {
        return match ($this->paroi->enum_periode_isolation_id) {
            1 => 1947,
            2 => 1974,
            3 => 1977,
            4 => 1982,
            5 => 1988,
            6 => 2000,
            7 => 2005,
            8 => 2012,
            9 => 2021,
            10 => $this->context->ressource()->administratif()->annee_etablissement(),
            default => null,
        };
    }
}
