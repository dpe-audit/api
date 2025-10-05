<?php

namespace App\Engine\Rules\ZoneThermique;

use App\Engine\Rule;

final class ZoneThermiqueRule extends Rule
{
    /**
     * Bâtiment chauffé majoritairement par effet joule
     */
    public function effet_joule(): bool
    {
        return $this->get('effet_joule', function () {
            return $this->data()->batiment->surface_chauffee_effet_joule() > $this->data()->batiment->surface_chauffee() / 2;
        });
    }

    /**
     * Surface habitable de référence exprimée en m²
     */
    public function surface_reference(): float
    {
        return $this->get('surface_reference', function () {
            return $this->data()->batiment->logements() === 1
                ? $this->data()->batiment->surface_habitable_logement()
                : $this->data()->batiment->surface_habitable_batiment();
        });
    }

    /**
     * Hauteur sous plafond de référence exprimée en mètre
     */
    public function hauteur_sous_plafond(): float
    {
        return $this->get('hauteur_sous_plafond', function () {
            return $this->data()->batiment->logements() === 1
                ? $this->data()->batiment->hauteur_sous_plafond_logement()
                : $this->data()->batiment->hauteur_sous_plafond_batiment();
        });
    }

    /**
     * Volume habitable de référence exprimé en m³
     */
    public function volume_reference(): float
    {
        return $this->get('volume_habitable', function () {
            return $this->surface_reference() * $this->hauteur_sous_plafond();
        });
    }

    /**
     * Nombre de logements de référence
     */
    public function nombre_logements(): int
    {
        return $this->data()->batiment->logements();
    }

    public function calcule(): void
    {
        $this->ressource()->calcule($this->ressource()->data()->with(
            effet_joule: $this->effet_joule(),
            surface_reference: $this->surface_reference(),
            volume_reference: $this->volume_reference(),
        ));
    }
}
