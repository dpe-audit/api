<?php

namespace App\Engine\Rules\Batiment;

use App\Domain\Batiment\TypeBatiment;
use App\Engine\Rules\WithInput;

trait WithBatiment
{
    use WithInput;

    public function type_batiment(): TypeBatiment
    {
        return $this->input()->batiment->type;
    }

    public function annee_construction(): int
    {
        return $this->input()->batiment->annee_construction;
    }

    public function altitude(): float
    {
        return $this->input()->batiment->altitude;
    }

    public function materiaux_anciens(): bool
    {
        return $this->input()->batiment->materiaux_anciens;
    }

    public function surface_habitable_logement(): ?float
    {
        return $this->input()->logement?->surface_habitable();
    }

    public function surface_habitable_batiment(): float
    {
        return $this->input()->batiment->surface_habitable;
    }

    public function surface_habitable(): float
    {
        return $this->surface_habitable_logement() ?? $this->surface_habitable_batiment();
    }

    public function surface_habitable_moyenne(): float
    {
        return $this->surface_habitable() / $this->logements();
    }

    public function hauteur_sous_plafond_logement(): ?float
    {
        return $this->input()->logement?->hauteur_sous_plafond();
    }

    public function hauteur_sous_plafond_batiment(): float
    {
        return $this->input()->batiment->hauteur_sous_plafond;
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->hauteur_sous_plafond_logement() ?? $this->hauteur_sous_plafond_batiment();
    }

    public function logements(): int
    {
        return $this->input()->batiment->logements;
    }

    public function code_departement(): string
    {
        return $this->input()->batiment->adresse->code_departement;
    }

    public function surface_chauffee_effet_joule(): float
    {
        return $this->input()->chauffage->installations()->with_effet_joule()->surface();
    }

    public function surface_chauffee(): float
    {
        return $this->input()->chauffage->installations()->surface();
    }
}
