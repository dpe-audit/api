<?php

namespace App\Database\Observatoire\Transformer\Enveloppe;

use App\Database\Observatoire\Model\XMLRessource;
use App\Domain\Enveloppe\Lnc\Baie\Mitoyennete;
use App\Domain\Enveloppe\Lnc\Baie\TypeVitrage;
use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Dto\Enveloppe\Lnc\Baie\BaieDto;
use App\Dto\Enveloppe\Lnc\Baie\PositionDto;
use App\Dto\Enveloppe\Lnc\LncDto;

final class EtsTransformer
{
    /**
     * @return array<LncDto>
     * 
     * Reconstitution des baies sur la base des parois associées à un espace tampon solarisé en
     * l'absence de baies déclarées dans le XML.
     */
    private function fromXMLEts(XMLRessource $ressource, array &$collection): array
    {
        foreach ($ressource->logement()->enveloppe->ets_collection as $ets) {
            /** @var array<BaieDto> $baies */
            $baies = [];
            /** @var array<float> */
            $orientations = [];

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
            if (count($baies) > 0) {
                continue;
            }
            foreach ($ressource->logement()->enveloppe->parois() as $paroi) {
                if (false === $paroi->lnc_id($ressource)->compare($ets->id())) {
                    continue;
                }
                if (in_array($paroi->orientation_baie_ets(), $orientations)) {
                    continue;
                }
                $orientations[] = $paroi->orientation_baie_ets();
                $baies[] = new BaieDto(
                    id: (string) $paroi->id(),
                    description: 'Baie reconstituée',
                    type_vitrage: $ets->type_vitrage(),
                    materiau: $ets->materiau(),
                    presence_rupteur_pont_thermique: $ets->presence_rupteur_pont_thermique(),
                    position: new PositionDto(
                        mitoyennete: Mitoyennete::EXTERIEUR,
                        surface: $paroi->surface_aue ?? 5,
                        orientation: $paroi->orientation_baie_ets(),
                        inclinaison: 90,
                    )
                );
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

    /**
     * @return array<LncDto>
     * 
     * Reconstitution des espaces tampons solarisées associées à une paroi associée à une
     * espace tampon solarisé non déclaré dans le XML.
     */
    private function fromXMLParoi(XMLRessource $ressource, array &$collection): array
    {
        foreach ($ressource->logement()->enveloppe->parois() as $paroi) {
            if (false === $paroi->has_espace_tampon_solarise()) {
                continue;
            }
            if ($ressource->logement()->enveloppe->find_ets((string) $paroi->lnc_id($ressource))) {
                continue;
            }
            $baies[] = new BaieDto(
                id: (string) $paroi->id(),
                description: 'Baie reconstituée',
                type_vitrage: TypeVitrage::SIMPLE_VITRAGE,
                materiau: null,
                presence_rupteur_pont_thermique: null,
                position: new PositionDto(
                    mitoyennete: Mitoyennete::EXTERIEUR,
                    surface: $paroi->surface_aue ?? 5,
                    orientation: $paroi->orientation_baie_ets() ?? 0,
                    inclinaison: 90,
                )
            );

            $collection[] = new LncDto(
                id: (string) $paroi->lnc_id($ressource),
                description: 'Espace tampon solarisé reconstitué',
                type: TypeLnc::ESPACE_TAMPON_SOLARISE,
                parois: [],
                baies: $baies,
            );
        }

        return $collection;
    }

    /**
     * @return array<LncDto>
     */
    public function __invoke(XMLRessource $ressource): array
    {
        $collection = [];
        $this->fromXMLEts($ressource, $collection);
        $this->fromXMLParoi($ressource, $collection);
        return $collection;
    }
}
