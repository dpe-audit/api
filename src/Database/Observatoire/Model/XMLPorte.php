<?php

namespace App\Database\Observatoire\Model;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Porte\Isolation;
use App\Domain\Enveloppe\Porte\Materiau;
use App\Domain\Enveloppe\Porte\Position\Mitoyennete;
use App\Domain\Enveloppe\Porte\TypePose;
use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;

final class XMLPorte extends XMLParoi
{
    public function __construct(
        public readonly ?string $reference_paroi,
        public readonly ?float $surface_porte,
        public readonly ?int $nb_porte,
        public readonly int $enum_type_porte_id,
        public readonly int $enum_type_pose_id,
        public readonly ?float $uporte_saisi,
        public readonly int $enum_methode_saisie_uporte_id,
        public readonly ?float $largeur_dormant,
        public readonly ?bool $presence_retour_isolation,
        public readonly bool $presence_joint,
        public readonly ?int $tv_uporte_id,
        public readonly float $uporte
    ) {}

    /**
     * XSD logement/enveloppe/porte_collection/porte
     */
    public static function from(\SimpleXMLElement $xml): self
    {
        return (new self(
            reference_paroi: (string) $xml->donnee_entree->reference_paroi ?: null,
            surface_porte: (float) $xml->donnee_entree->surface_porte ?: null,
            nb_porte: (int) $xml->donnee_entree->nb_porte ?: null,
            enum_type_porte_id: (int) $xml->donnee_entree->enum_type_porte_id,
            enum_type_pose_id: (int) $xml->donnee_entree->enum_type_pose_id,
            uporte_saisi: (float) $xml->donnee_entree->uporte_saisi ?: null,
            enum_methode_saisie_uporte_id: (int) $xml->donnee_entree->enum_methode_saisie_uporte_id,
            largeur_dormant: (float) $xml->donnee_entree->largeur_dormant ?: null,
            presence_retour_isolation: (bool)(int) $xml->donnee_entree->presence_retour_isolation ?: null,
            presence_joint: (bool)(int) $xml->donnee_entree->presence_joint,
            tv_uporte_id: (int) $xml->donnee_entree->tv_uporte_id ?: null,
            uporte: (float) $xml->donnee_intermediaire->uporte
        ))->set($xml);
    }

    /**
     * XSD logement/enveloppe/porte_collection
     * 
     * @return array<self>
     */
    public static function from_collection(\SimpleXMLElement $xml): array
    {
        $collection = [];

        foreach ($xml->porte as $item) {
            $collection[] = self::from($item);
        }
        return $collection;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->enum_cfg_isolation_lnc_id === 1
            ? Mitoyennete::LOCAL_NON_ACCESSIBLE
            : match ($this->enum_type_adjacence_id) {
                1 => Mitoyennete::EXTERIEUR,
                2 => Mitoyennete::ENTERRE,
                3 => Mitoyennete::VIDE_SANITAIRE,
                4 => Mitoyennete::LOCAL_NON_RESIDENTIEL,
                5 => Mitoyennete::TERRE_PLEIN,
                6 => Mitoyennete::SOUS_SOL_NON_CHAUFFE,
                7 => Mitoyennete::LOCAL_NON_ACCESSIBLE,
                8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21 => Mitoyennete::LOCAL_NON_CHAUFFE,
                20 => Mitoyennete::LOCAL_NON_RESIDENTIEL,
                22 => Mitoyennete::LOCAL_RESIDENTIEL,
            };
    }

    public function isolation(): ?Isolation
    {
        return match ($this->enum_type_porte_id) {
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 => Isolation::NON_ISOLE,
            13, 15 => Isolation::ISOLE,
            default => null,
        };
    }

    public function materiau(): ?Materiau
    {
        return match ($this->enum_type_porte_id) {
            1, 2, 3, 4 => Materiau::BOIS,
            5, 6, 7, 8 => Materiau::PVC,
            9, 10, 11, 12 => Materiau::METAL,
            default => null,
        };
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return match ($this->enum_type_porte_id) {
            2, 3, 6, 7, 10, 11 => TypeVitrage::SIMPLE_VITRAGE,
            4, 8, 12, 15 => TypeVitrage::DOUBLE_VITRAGE,
            1, 5, 9, 13, 14, 16 => null,
        };
    }

    public function surface_vitrage(): float
    {
        return match ($this->enum_type_porte_id) {
            2, 6, 11 => $this->surface() * 0.15,
            3, 7, 12 => $this->surface() * 0.45,
            4, 8, 10 => $this->surface() * 0.30,
            default => 0,
        };
    }

    public function presence_sas(): bool
    {
        return $this->enum_type_porte_id === 14;
    }

    public function type_pose(): ?TypePose
    {
        return match ($this->enum_type_pose_id) {
            1 => TypePose::NU_EXTERIEUR,
            2 => TypePose::NU_INTERIEUR,
            3 => TypePose::TUNNEL,
            4 => null,
        };
    }

    public function nb_porte(): int
    {
        return $this->nb_porte ?? 1;
    }

    public function surface(): float
    {
        return $this->surface_porte / $this->nb_porte();
    }

    public function largeur_dormant(): ?int
    {
        return $this->largeur_dormant ? $this->largeur_dormant * 10 : null;
    }

    public function paroi_id(XMLRessource $xml): ?Id
    {
        if ($this->reference_paroi) {
            return $xml->logement()->enveloppe->find_mur($this->reference_paroi)?->id()
                ?? $xml->logement()->enveloppe->find_plancher_haut($this->reference_paroi)?->id()
                ?? $xml->logement()->enveloppe->find_plancher_bas($this->reference_paroi)?->id()
                ?? null;
        }
        return null;
    }
}
