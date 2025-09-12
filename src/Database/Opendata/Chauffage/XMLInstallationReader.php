<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\{XMLElement, XMLReader};
use App\Model\Chauffage\Enum\UsageChauffage;
use App\Model\Chauffage\ValueObject\{Regulation, Solaire};
use App\Model\Common\ValueObject\{Id, Pourcentage};

final class XMLInstallationReader extends XMLReader
{
    public bool $is_appoint_electrique_sdb = false;

    /**
     * Les installations avec vhauffage électrique dans la salle de bain sont considérées
     * comme deux installations distinctes
     */
    public function supports(): bool
    {
        return false === $this->has_appoint_electrique_sdb();
    }

    /**
     * @return XMLEmetteurReader[]
     */
    public function emetteurs(): array
    {
        return array_filter(
            array_map(
                fn(XMLElement $xml): XMLEmetteurReader => XMLEmetteurReader::from($xml),
                $this->findMany('.//emetteur_chauffage_collection//emetteur_chauffage'),
            ),
            fn(XMLEmetteurReader $reader): bool => $reader->supports(),
        );
    }

    public function solaire_thermique(): ?Solaire
    {
        return $this->usage_solaire() ? new Solaire(
            usage: $this->usage_solaire(),
            annee_installation: $this->annee_installation_solaire(),
            fch: $this->fch_saisi(),
        ) : null;
    }

    public function regulation_centrale(): Regulation
    {
        return Regulation::create(
            presence_regulation: $this->presence_regulation_centrale(),
            minimum_temperature: $this->regulation_centrale_minimum_temperature(),
            detection_presence: $this->regulation_centrale_detection_presence(),
        );
    }

    public function regulation_terminale(): Regulation
    {
        return Regulation::create(
            presence_regulation: $this->presence_regulation_terminale(),
            minimum_temperature: $this->regulation_terminale_minimum_temperature(),
            detection_presence: $this->regulation_terminale_detection_presence(),
        );
    }

    /**
     * Reconstitution des données pour les configuration avec générateur électrique dans la salle de bain
     */
    public function has_appoint_electrique_sdb(): bool
    {
        return \in_array($this->enum_cfg_installation_ch_id(), [4, 5]);
    }

    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Installation non décrite';
    }

    /**
     * En présence d'un appoint électrique dans la salle de bain, on considère une surface déduite de 10%
     */
    public function surface(): float
    {
        if ($this->is_appoint_electrique_sdb) {
            return $this->findOneOrError('.//surface_chauffee')->floatval() * 0.1;
        }
        return $this->has_appoint_electrique_sdb()
            ? $this->findOneOrError('.//surface_chauffee')->floatval() * 0.9
            : $this->findOneOrError('.//surface_chauffee')->floatval();
    }

    /**
     * En l'absence d'émetteurs, on considère la présence d'un comptage individuel (émission directe)
     */
    public function comptage_individuel(): ?bool
    {
        foreach ($this->emetteurs() as $item) {
            if ($item->comptage_individuel() !== null) {
                return $item->comptage_individuel();
            }
        }
        return true;
    }

    public function usage_solaire(): ?UsageChauffage
    {
        return \in_array($this->enum_cfg_installation_ch_id(), [2, 7])
            ? UsageChauffage::CHAUFFAGE
            : null;
    }

    public function annee_installation_solaire(): ?Id
    {
        return null;
    }

    public function presence_regulation_centrale(): bool
    {
        foreach ($this->emetteurs() as $reader) {
            if (true === $reader->presence_regulation_centrale()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_centrale_minimum_temperature(): bool
    {
        foreach ($this->emetteurs() as $reader) {
            if (true === $reader->regulation_centrale_minimum_temperature()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_centrale_detection_presence(): bool
    {
        foreach ($this->emetteurs() as $reader) {
            if (true === $reader->regulation_centrale_detection_presence()) {
                return true;
            }
        }
        return false;
    }

    public function presence_regulation_terminale(): bool
    {
        foreach ($this->emetteurs() as $reader) {
            if (true === $reader->presence_regulation_terminale()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_terminale_minimum_temperature(): bool
    {
        foreach ($this->emetteurs() as $reader) {
            if (true === $reader->regulation_terminale_minimum_temperature()) {
                return true;
            }
        }
        return false;
    }

    public function regulation_terminale_detection_presence(): bool
    {
        foreach ($this->emetteurs() as $reader) {
            if (true === $reader->regulation_terminale_detection_presence()) {
                return true;
            }
        }
        return false;
    }

    public function installation_collective(): bool
    {
        return $this->enum_type_installation_id() === 2;
    }

    public function niveaux_desservis(): int
    {
        return $this->findOneOrError('.//nombre_niveau_installation_ch')->intval();
    }

    public function enum_cfg_installation_ch_id(): int
    {
        return $this->findOneOrError('.//enum_cfg_installation_ch_id')->intval();
    }

    public function enum_type_installation_id(): int
    {
        return $this->findOneOrError('.//enum_type_installation_id')->intval();
    }

    public function fch_saisi(): ?Pourcentage
    {
        return ($value = $this->findOne('.//fch_saisi')?->floatval())
            ? Pourcentage::from($value)
            : null;
    }
}
