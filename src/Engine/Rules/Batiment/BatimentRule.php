<?php

namespace App\Engine\Rules\Batiment;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};
use App\Domain\Diagnostic\Diagnostic;
use App\Domain\Enveloppe\Inertie;
use App\Domain\Scenario\Etape\Etape;
use App\Engine\{Context, Rule};
use App\Engine\Rules\Enveloppe\WithInertieRule;
use App\Engine\Table\SollicitationsClimatiquesTableValeurRepository;

final class BatimentRule extends Rule
{
    use WithBatiment, WithInertieRule;

    public function __construct(
        private SollicitationsClimatiquesTableValeurRepository $repository,
    ) {}

    /**
     * Bâtiment chauffé majoritairement par effet joule
     */
    public function effet_joule(): bool
    {
        return $this->get('effet_joule', function () {
            return $this->surface_chauffee_effet_joule() > $this->surface_chauffee() / 2;
        });
    }

    /**
     * Surface habitable de référence exprimée en m²
     */
    public function surface_reference(): float
    {
        return $this->get('surface_reference', function () {
            return $this->surface_habitable_logement() ?? $this->surface_habitable_batiment();
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
     * Zone climatique
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->get('zone_climatique', function (): ZoneClimatique {
            return ZoneClimatique::from_code_departement($this->code_departement());
        });
    }

    /**
     * Température extérieure de base exprimée en °C
     */
    public function tbase(): float
    {
        return $this->get('tbase', function () {
            if (null === $tbase = $this->repository->tbase(
                zone_climatique: $this->zone_climatique(),
                altitude: $this->altitude(),
            )) {
                throw new \DomainException("Valeur forfaitaire Tbase non trouvée");
            }
            return $tbase;
        });
    }

    /**
     * Parois anciennes lourdes
     */
    public function parois_anciennes_lourdes(): bool
    {
        return $this->get('parois_anciennes_lourdes', function () {
            return $this->materiaux_anciens() && \in_array($this->inertie(), [
                Inertie::TRES_LOURDE,
                Inertie::LOURDE,
            ]);
        });
    }

    public function epv(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures($mois)->epv;
    }

    public function e(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures($mois)->e;
    }

    /**
     * Ensoleillement reçu en période de refroidissement sur le mois par une paroi verticale orientée 
     * au sud en absence d’ombrage en kWh/m² pour une température de consigne de 26°C
     */
    public function e_fr_26(Mois $mois): float
    {
        return $this->sollicitations_exterieures($mois)->efr26;
    }

    /**
     * Ensoleillement reçu en période de refroidissement sur le mois par une paroi verticale orientée 
     * au sud en absence d’ombrage en kWh/m² pour une température de consigne de 28°C
     */
    public function e_fr_28(Mois $mois): float
    {
        return $this->sollicitations_exterieures($mois)->efr28;
    }

    /**
     * Ensoleillement reçu en période de refroidissement sur le mois par une paroi verticale orientée 
     * au sud en absence d’ombrage en kWh/m²
     * 
     * TODO: Vérifier la méthode (coquille ?)
     */
    public function e_fr(Mois $mois): ?float
    {
        return match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => $this->e_fr_28($mois),
            ScenarioUsage::DEPENSIER => $this->e_fr_26($mois),
        };
    }

    public function nref_19(Mois $mois): float
    {
        return $this->sollicitations_exterieures($mois)->nref19;
    }

    public function nref_21(Mois $mois): float
    {
        return $this->sollicitations_exterieures($mois)->nref21;
    }

    public function nref(Mois $mois): ?float
    {
        return match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures($mois)->nref19,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures($mois)->nref21,
        };
    }

    /**
     * Nref_fr - Nombre d'heures de refroidissement sur le mois en h
     */
    public function nref_fr(Mois $mois): ?float
    {
        return match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures($mois)->nref28,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures($mois)->nref26,
        };
    }

    /**
     * DH - Degrés-heures de chauffage sur le mois en °C.h
     */
    public function dh(Mois $mois): ?float
    {
        return match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures($mois)->dh19,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures($mois)->dh21,
        };
    }

    /**
     * DH14 - Degrés heures de base 14 sur la saison de chauffe complète °C.h
     */
    public function dh14(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures($mois)->dh14;
    }

    /**
     * Text- Température extérieure moyenne en période de chauffe sur le mois en C°
     */
    public function text(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures($mois)->text;
    }

    /**
     * Text_fr - Température extérieure moyenne en période de refroidissement sur le mois en C°
     */
    public function text_fr(Mois $mois): ?float
    {
        return match ($this->scenario()) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures($mois)->textmoy_clim28,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures($mois)->textmoy_clim26,
        };
    }

    /**
     * tefs - Température moyenne d'eau froide sanitaire sur le mois en °C
     */
    public function tefs(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures($mois)->tefs;
    }

    /**
     * Sollicitations extérieures pour chaque mois de l'année
     * 
     * @return SollicitationsExterieures
     */
    public function sollicitations_exterieures(Mois $mois): SollicitationsExterieures
    {
        $collection = $this->get('sollicitations_exterieures', function (): array {
            return $this->repository->sollicitations_exterieures(
                zone_climatique: $this->zone_climatique(),
                altitude: $this->altitude(),
                parois_anciennes_lourdes: $this->parois_anciennes_lourdes(),
            );
        });
        return $this->get("ext::{$mois->value}", function () use ($mois, $collection) {
            $value = array_find($collection, fn(SollicitationsExterieures $item) => $item->mois === $mois);
            return $value ?? new \DomainException("Sollicitations extérieures non trouvées");
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        if (!$data instanceof Diagnostic || !$data instanceof Etape) {
            return;
        }
        $data->calcule($data->data()->with(
            zone_climatique: $this->zone_climatique(),
            effet_joule: $this->effet_joule(),
            parois_anciennes_lourdes: $this->parois_anciennes_lourdes(),
            surface_reference: $this->surface_reference(),
            volume_reference: $this->volume_reference(),
        ));
    }
}
