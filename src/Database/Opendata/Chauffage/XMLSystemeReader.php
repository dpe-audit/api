<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLReader;
use App\Model\Chauffage\Enum\{IsolationReseau, TypeChauffage, TypeDistribution};
use App\Model\Chauffage\ValueObject\Reseau;
use App\Model\Common\ValueObject\Id;

final class XMLSystemeReader extends XMLReader
{
    public function generateur(): XMLGenerateurReader
    {
        return XMLGenerateurReader::from($this->xml());
    }

    public function installation(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->findOneOrError('./ancestor::installation_chauffage'));
    }

    /**
     * @return XMLEmetteurReader[]
     */
    public function emetteurs(): array
    {
        return array_filter(
            $this->chauffage()->emetteurs(),
            fn(XMLEmetteurReader $item): bool => $item->supports() && $item->enum_lien_generateur_emetteur_id() === $this->enum_lien_generateur_emetteur_id(),
        );
    }

    public function reseau(): ?Reseau
    {
        return $this->type_distribution() ? Reseau::create(
            type_distribution: $this->type_distribution(),
            isolation: $this->isolation_reseau(),
            niveaux_desservis: $this->niveaux_desservis(),
            presence_circulateur_externe: $this->presence_circulateur_externe(),
        ) : null;
    }

    public function id(): Id
    {
        return $this->generateur()->id();
    }

    public function generateur_id(): Id
    {
        return $this->generateur()->id();
    }

    public function installation_id(): Id
    {
        return $this->installation()->id();
    }

    public function type_chauffage(): TypeChauffage
    {
        return count($this->emetteurs()) > 0 ? TypeChauffage::CHAUFFAGE_CENTRAL : TypeChauffage::CHAUFFAGE_DIVISE;
    }

    public function type_distribution(): ?TypeDistribution
    {
        foreach ($this->emetteurs() as $reader) {
            if ($reader->type_distribution()) {
                return $reader->type_distribution();
            }
        }
        return null;
    }

    public function presence_circulateur_externe(): ?bool
    {
        return true === $this->installation()->installation_collective()
            && $this->installation()->findOne('//conso_auxiliaire_distribution_ch')?->floatval() > 0;
    }

    public function niveaux_desservis(): ?int
    {
        return $this->installation()->niveaux_desservis();
    }

    public function isolation_reseau(): ?IsolationReseau
    {
        foreach ($this->emetteurs() as $reader) {
            if ($reader->reseau_distribution_isole() !== null) {
                return $reader->reseau_distribution_isole() ? IsolationReseau::ISOLE : IsolationReseau::NON_ISOLE;
            }
        }
        return null;
    }

    public function enum_lien_generateur_emetteur_id(): int
    {
        return $this->generateur()->enum_lien_generateur_emetteur_id();
    }
}
