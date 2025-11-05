<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Paroi\Inertie;
use App\Dto\Enveloppe\Niveau\NiveauDto;
use App\Legacy\Transformer\Context;

final class NiveauTransformer
{
    private Context $context;

    public function surface(): float
    {
        return $this->context->ressource()->logement()->caracteristique_generale->surface_habitable_immeuble
            ?? $this->context->ressource()->logement()->caracteristique_generale->surface_habitable_logement;
    }

    public function inertie_paroi_verticale(): Inertie
    {
        return $this->context->ressource()->logement()->enveloppe->inertie_paroi_verticale_lourd
            ? Inertie::LOURDE
            : Inertie::LEGERE;
    }

    public function inertie_plancher_haut(): Inertie
    {
        return $this->context->ressource()->logement()->enveloppe->inertie_plancher_haut_lourd
            ? Inertie::LOURDE
            : Inertie::LEGERE;
    }

    public function inertie_plancher_bas(): Inertie
    {
        return $this->context->ressource()->logement()->enveloppe->inertie_plancher_bas_lourd
            ? Inertie::LOURDE
            : Inertie::LEGERE;
    }

    public function __invoke(Context $context): NiveauDto
    {
        $this->context = $context;

        return new NiveauDto(
            id: (string) Id::create(),
            description: 'Niveau reconstitué',
            surface: $this->surface(),
            inertie_paroi_verticale: $this->inertie_paroi_verticale(),
            inertie_plancher_haut: $this->inertie_plancher_haut(),
            inertie_plancher_bas: $this->inertie_plancher_bas(),
        );
    }
}
