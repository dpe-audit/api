<?php

namespace App\Legacy\Model\Sortie;

final class ConfortEte
{
    public function __construct(
        public readonly ?bool $isolation_toiture,
        public readonly ?bool $protection_solaire_exterieure,
        public readonly ?bool $aspect_traversant,
        public readonly ?bool $brasseur_air,
        public readonly ?bool $inertie_lourde,
        public readonly int $enum_indicateur_confort_ete_id
    ) {}

    /**
     * @param \SimpleXMLElement $xml - XPATH logement/sortie/confort_ete
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return new self(
            isolation_toiture: (bool)(int) $xml->isolation_toiture ?: null,
            protection_solaire_exterieure: (bool)(int) $xml->protection_solaire_exterieure ?: null,
            aspect_traversant: (bool)(int) $xml->aspect_traversant ?: null,
            brasseur_air: (bool)(int) $xml->brasseur_air ?: null,
            inertie_lourde: (bool)(int) $xml->inertie_lourde,
            enum_indicateur_confort_ete_id: (int) $xml->enum_indicateur_confort_ete_id
        );
    }

    public function presence_brasseurs_air(): bool
    {
        return (bool) $this->brasseur_air;
    }
}
