<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\PontThermique\Liaison\TypeLiaison;
use App\Dto\Enveloppe\PontThermique\{LiaisonDto, PontThermiqueDto};
use App\Legacy\Model\PontThermique;
use App\Legacy\Transformer\Context;

final class PontThermiqueTransformer
{
    private Context $context;
    private PontThermique $pont_thermique;

    public function type_liaison(): TypeLiaison
    {
        return match ($this->pont_thermique->enum_type_liaison_id) {
            1 => TypeLiaison::PLANCHER_BAS_MUR,
            2 => TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR,
            3 => TypeLiaison::PLANCHER_HAUT_MUR,
            4 => TypeLiaison::REFEND_MUR,
            5 => TypeLiaison::MENUISERIE_MUR
        };
    }

    public function pont_thermique_partiel(): bool
    {
        return $this->pont_thermique->pourcentage_valeur_pont_thermique < 1;
    }

    public function kpt(): ?float
    {
        if ($this->pont_thermique->k_saisi > 0) {
            return $this->pont_thermique->k_saisi;
        }
        if (in_array($this->type_liaison(), [TypeLiaison::PLANCHER_BAS_MUR, TypeLiaison::PLANCHER_HAUT_MUR,])) {
            return false === $this->plancher_id() ? $this->pont_thermique->k_saisi : null;
        }
        if ($this->type_liaison() !== TypeLiaison::MENUISERIE_MUR) {
            return false === $this->ouverture_id() ? $this->pont_thermique->k_saisi : null;
        }
        return null;
    }

    public function mur_id(): string
    {
        $id = null;

        if ($this->pont_thermique->reference_1) {
            $id = $this->context->logement()->enveloppe->match_mur($this->pont_thermique->reference_1)?->id();
        }
        if ($this->pont_thermique->reference_2) {
            $id = $id ?? $this->context->logement()->enveloppe->match_mur($this->pont_thermique->reference_2)?->id();
        }
        return $id ?? current($this->context->logement()->enveloppe->mur_collection)->id();
    }

    public function plancher_id(bool $default = false): ?string
    {
        if (!in_array($this->type_liaison(), [TypeLiaison::PLANCHER_BAS_MUR, TypeLiaison::PLANCHER_HAUT_MUR,])) {
            return null;
        }
        $id = null;

        if ($this->pont_thermique->reference_1) {
            $id = $this->context->logement()->enveloppe->match_plancher_bas($this->pont_thermique->reference_1)?->id()
                ?? $this->context->logement()->enveloppe->match_plancher_haut($this->pont_thermique->reference_1)?->id();
        }

        if ($this->pont_thermique->reference_2) {
            $id = $id ?? $this->context->logement()->enveloppe->match_plancher_bas($this->pont_thermique->reference_2)?->id()
                ?? $this->context->logement()->enveloppe->match_plancher_haut($this->pont_thermique->reference_2)?->id();
        }

        if ($default) {
            $id = $id ?? current($this->context->logement()->enveloppe->plancher_bas_collection)?->id();
            $id = $id ?? current($this->context->logement()->enveloppe->plancher_haut_collection)?->id();
        }
        return $id;
    }

    public function ouverture_id(bool $default = false): ?string
    {
        if ($this->type_liaison() !== TypeLiaison::MENUISERIE_MUR) {
            return null;
        }
        $id = null;

        if ($this->pont_thermique->reference_1) {
            $id = $this->context->logement()->enveloppe->match_baie_vitree($this->pont_thermique->reference_1)?->id()
                ?? $this->context->logement()->enveloppe->match_porte($this->pont_thermique->reference_1)?->id();
        }
        if ($this->pont_thermique->reference_2) {
            $id = $id ?? $this->context->logement()->enveloppe->match_baie_vitree($this->pont_thermique->reference_2)?->id()
                ?? $this->context->logement()->enveloppe->match_porte($this->pont_thermique->reference_2)?->id();
        }
        if ($default) {
            $id = $id ?? current($this->context->logement()->enveloppe->baie_vitree_collection)?->id();
            $id = $id ?? current($this->context->logement()->enveloppe->porte_collection)?->id();
        }
        return $id;
    }

    public function __invoke(PontThermique $pont_thermique, Context $context): PontThermiqueDto
    {
        $this->context = $context;
        $this->pont_thermique = $pont_thermique;

        return new PontThermiqueDto(
            id: $pont_thermique->id(),
            description: $pont_thermique->description(),
            longueur: $pont_thermique->l,
            kpt: $this->kpt(),
            liaison: new LiaisonDto(
                type: $this->type_liaison(),
                pont_thermique_partiel: $this->pont_thermique_partiel(),
                mur_id: $this->mur_id(),
                plancher_id: $this->plancher_id(true),
                ouverture_id: $this->ouverture_id(true),
            )
        );
    }
}
