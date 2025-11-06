<?php

namespace App\Engine\Rules\Batiment;

use App\Domain\Batiment\ZoneClimatique;
use App\Domain\Common\Enum\Mois;
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

    public function e_fr(Mois $mois): ?float
    {
        return $this->batiment_rule()->e_fr($mois);
    }

    public function nref(Mois $mois): ?float
    {
        return $this->batiment_rule()->nref($mois);
    }

    public function nref_fr(Mois $mois): ?float
    {
        return $this->batiment_rule()->nref_fr($mois);
    }

    public function dh(Mois $mois): ?float
    {
        return $this->batiment_rule()->dh($mois);
    }

    public function dh14(Mois $mois): ?float
    {
        return $this->batiment_rule()->dh14($mois);
    }

    public function text(Mois $mois): ?float
    {
        return $this->batiment_rule()->text($mois);
    }

    public function text_fr(Mois $mois): ?float
    {
        return $this->batiment_rule()->text_fr($mois);
    }

    public function tefs(Mois $mois): ?float
    {
        return $this->batiment_rule()->tefs($mois);
    }
}
