<?php

namespace App\Engine\Rules\Batiment;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Common\Enum\{Mois, Scenario};
use App\Engine\Rules\WithRule;

trait WithBatimentRule
{
    use WithRule;

    private function batiment_rule(): BatimentRule
    {
        return $this->require(BatimentRule::class);
    }

    public function effet_joule(): bool
    {
        return $this->batiment_rule()->effet_joule();
    }

    public function surface_reference(): float
    {
        return $this->batiment_rule()->surface_reference();
    }

    public function volume_reference(): float
    {
        return $this->batiment_rule()->volume_reference();
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->batiment_rule()->zone_climatique();
    }

    public function tbase(): float
    {
        return $this->batiment_rule()->tbase();
    }

    public function parois_anciennes_lourdes(): bool
    {
        return $this->batiment_rule()->parois_anciennes_lourdes();
    }

    public function epv(Mois $mois): ?float
    {
        return $this->batiment_rule()->epv($mois);
    }

    public function e(Mois $mois): ?float
    {
        return $this->batiment_rule()->e($mois);
    }

    public function e_fr(Scenario $scenario, Mois $mois): ?float
    {
        return $this->batiment_rule()->e_fr($scenario, $mois);
    }

    public function nref(Scenario $scenario, Mois $mois): ?float
    {
        return $this->batiment_rule()->nref($scenario, $mois);
    }

    public function nref_fr(Scenario $scenario, Mois $mois): ?float
    {
        return $this->batiment_rule()->nref_fr($scenario, $mois);
    }

    public function dh(Scenario $scenario, Mois $mois): ?float
    {
        return $this->batiment_rule()->dh($scenario, $mois);
    }

    public function dh14(Mois $mois): ?float
    {
        return $this->batiment_rule()->dh14($mois);
    }

    public function text(Mois $mois): ?float
    {
        return $this->batiment_rule()->text($mois);
    }

    public function text_fr(Scenario $scenario, Mois $mois): ?float
    {
        return $this->batiment_rule()->text_fr($scenario, $mois);
    }

    public function tefs(Mois $mois): ?float
    {
        return $this->batiment_rule()->tefs($mois);
    }
}
