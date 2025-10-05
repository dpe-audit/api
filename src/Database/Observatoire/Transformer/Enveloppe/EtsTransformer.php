<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Dto\Enveloppe\Lnc\Baie\BaieDto;
use App\Dto\Enveloppe\Lnc\Baie\PositionDto;
use App\Dto\Enveloppe\Lnc\LncDto;

final class EtsTransformer
{
    /**
     * @return array<LncDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];

        foreach ($ressource->logement()->enveloppe->ets_collection as $ets) {
            /** @var array<BaieDto> $baies */
            $baies = [];
            foreach ($ets->ets_baie_collection as $baie_ets) {
                if (0 === $baie_ets->surface()) {
                    continue;
                }
                for ($i = 0; $i < $baie_ets->nb_baie; $i++) {
                    $collection[] = new BaieDto(
                        id: $baie_ets->id(),
                        description: $baie_ets->description(),
                        type_vitrage: $ets->type_vitrage(),
                        materiau: $ets->materiau(),
                        presence_rupteur_pont_thermique: $ets->presence_rupteur_pont_thermique(),
                        position: new PositionDto(
                            mitoyennete: $baie_ets->mitoyennete(),
                            surface: $baie_ets->surface(),
                            orientation: $baie_ets->orientation(),
                            inclinaison: $baie_ets->inclinaison(),
                        )
                    );
                }
            }
            $collection[] = new LncDto(
                id: (string) $ets->id(),
                description: $ets->description(),
                type: TypeLnc::ESPACE_TAMPON_SOLARISE,
                parois: [],
                baies: $baies,
            );
        }
        return $collection;
    }
}
