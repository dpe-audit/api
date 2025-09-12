<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Lnc\Paroi\Mitoyennete;
use App\Dto\Enveloppe\Lnc\LncDto;
use App\Dto\Enveloppe\Lnc\Paroi\ParoiDto;
use App\Dto\Enveloppe\Lnc\Paroi\PositionDto;

final class LncTransformer
{
    /**
     * @return array<LncDto>
     * 
     * Reconstitution des locaux non chauffés associés à une paroi donnant sur un local non chauffé.
     */
    public function __invoke(XMLRessource $xml): array
    {
        $collection = [];

        foreach ($xml->logement()->enveloppe->parois() as $paroi) {
            if (false === $paroi->has_local_non_chauffe()) {
                continue;
            }
            if ($paroi->has_espace_tampon_solarise()) {
                continue;
            }
            if (0 == $paroi->surface_aue) {
                continue;
            }
            $parois[] = new ParoiDto(
                id: (string) Id::create(),
                description: 'Paroi reconstituée',
                isolation: $paroi->isolation_paroi_lnc(),
                position: new PositionDto(
                    surface: $paroi->surface_aue,
                    mitoyennete: Mitoyennete::EXTERIEUR,
                )
            );

            if ($paroi->surface() < $paroi->surface_aiu) {
                $parois[] = new ParoiDto(
                    id: (string) $paroi->id(),
                    description: 'Paroi reconstituée',
                    isolation: $paroi->isolation_paroi_lnc(),
                    position: new PositionDto(
                        mitoyennete: Mitoyennete::EXTERIEUR,
                        surface: $paroi->surface_aiu - $paroi->surface(),
                    ),
                );
            }

            $collection[] = new LncDto(
                id: (string) $paroi->id(),
                description: 'Local non chauffé reconstitué',
                type: $paroi->type_lnc(),
                parois: $parois,
                baies: [],
            );
        }
        return $collection;
    }
}
