<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Lnc\Paroi\{Isolation, Mitoyennete};
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Dto\Enveloppe\Lnc\LncDto;
use App\Dto\Enveloppe\Lnc\Paroi\{ParoiDto, PositionDto};
use App\Legacy\Model\Paroi;
use App\Legacy\Transformer\Context;

/**
 * Reconstitution d'un local non chauffé
 */
final class LncTransformer
{
    private Paroi $paroi;

    public function type_lnc(): ?TypeLnc
    {
        return match ($this->paroi->enum_type_adjacence_id) {
            8 => TypeLnc::GARAGE,
            9 => TypeLnc::CELLIER,
            10 => TypeLnc::ESPACE_TAMPON_SOLARISE,
            11 => TypeLnc::COMBLE_FORTEMENT_VENTILE,
            12 => TypeLnc::COMBLE_FAIBLEMENT_VENTILE,
            13 => TypeLnc::COMBLE_TRES_FAIBLEMENT_VENTILE,
            14 => TypeLnc::CIRCULATION_SANS_OUVERTURE_EXTERIEURE,
            15 => TypeLnc::CIRCULATION_AVEC_OUVERTURE_EXTERIEURE,
            16 => TypeLnc::CIRCULATION_AVEC_BOUCHE_OU_GAINE_DESENFUMAGE_OUVERTE,
            17 => TypeLnc::HALL_ENTREE_AVEC_FERMETURE_AUTOMATIQUE,
            18 => TypeLnc::HALL_ENTREE_SANS_FERMETURE_AUTOMATIQUE,
            19 => TypeLnc::GARAGE_COLLECTIF,
            21 => TypeLnc::AUTRES,
            default => null,
        };
    }

    public function isolation_paroi_lnc(): ?Isolation
    {
        return match ($this->paroi->enum_cfg_isolation_lnc_id) {
            2, 4 => Isolation::NON_ISOLE,
            3, 5 => Isolation::ISOLE,
            default => null,
        };
    }

    /**
     * @return array<ParoiDto>
     */
    public function parois(): array
    {
        $collection = [];
        $collection[] = new ParoiDto(
            id: (string) Id::create(),
            description: 'Paroi reconstituée',
            isolation: $this->isolation_paroi_lnc(),
            position: new PositionDto(
                surface: $this->paroi->surface_aue,
                mitoyennete: Mitoyennete::EXTERIEUR,
            )
        );

        if ($this->paroi->surface() < $this->paroi->surface_aiu) {
            $collection[] = new ParoiDto(
                id: (string) Id::create(),
                description: 'Paroi reconstituée',
                isolation: $this->isolation_paroi_lnc(),
                position: new PositionDto(
                    mitoyennete: Mitoyennete::EXTERIEUR,
                    surface: $this->paroi->surface_aiu - $this->paroi->surface(),
                ),
            );
        }

        return $collection;
    }

    public function __invoke(Paroi $paroi, Context $context): ?LncDto
    {
        $this->paroi = $paroi;

        if (null === $type = $this->type_lnc()) {
            return null;
        }
        if ($type === TypeLnc::ESPACE_TAMPON_SOLARISE) {
            return null;
        }
        if ($paroi->reference_lnc && $context->logement()->enveloppe->match_ets($paroi->reference_lnc)) {
            return null;
        }
        if (0 == $paroi->surface_aue) {
            return null;
        }
        return new LncDto(
            id: $paroi->id(),
            description: 'Local non chauffé reconstitué',
            type: $type,
            parois: $this->parois(),
            baies: [],
        );
    }
}
