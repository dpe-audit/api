<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Baie\Baie;
use App\Domain\Enveloppe\DoubleFenetre\DoubleFenetre;
use App\Domain\Enveloppe\{Exposition, Inertie};
use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Masque\Masque;
use App\Domain\Enveloppe\Mur\Mur;
use App\Domain\Enveloppe\Niveau\Niveau;
use App\Domain\Enveloppe\PlancherBas\PlancherBas;
use App\Domain\Enveloppe\PlancherHaut\PlancherHaut;
use App\Domain\Enveloppe\PontThermique\PontThermique;
use App\Domain\Enveloppe\Porte\Porte;
use App\Engine\{Engine, Input};
use App\Engine\Rules\Apport\ApportEnveloppeRule;
use App\Engine\Rules\Deperdition\{DeperditionEnveloppeRule, DeperditionRenouvellementAirRule};
use App\Engine\Rules\Inertie\InertieEnveloppeRule;

final class EnveloppeInput extends Input
{
    /** @var NiveauInput[] */
    public readonly array $niveaux;
    /** @var LncInput[] */
    public readonly array $locaux_non_chauffes;
    /** @var DoubleFenetreInput[] */
    public readonly array $doubles_fenetres;
    /** @var MasqueInput[] */
    public readonly array $masques;
    /** @var MurInput[] */
    public readonly array $murs;
    /** @var PlancherBasInput[] */
    public readonly array $planchers_bas;
    /** @var PlancherHautInput[] */
    public readonly array $planchers_hauts;
    /** @var BaieInput[] */
    public readonly array $baies;
    /** @var PorteInput[] */
    public readonly array $portes;
    /** @var PontThermiqueInput[] */
    public readonly array $ponts_thermiques;

    public function __construct(public readonly Engine $context)
    {
        $this->niveaux = $context->ressource()->enveloppe()->niveaux()
            ->map(fn(Niveau $item) => new NiveauInput($context, $item))
            ->values();
        $this->locaux_non_chauffes = $context->ressource()->enveloppe()->locaux_non_chauffes()
            ->map(fn(Lnc $item) => new LncInput($context, $item))
            ->values();
        $this->doubles_fenetres = $context->ressource()->enveloppe()->doubles_fenetres()
            ->map(fn(DoubleFenetre $item) => new DoubleFenetreInput($context, $item))
            ->values();
        $this->masques = $context->ressource()->enveloppe()->masques()
            ->map(fn(Masque $item) => new MasqueInput($context, $item))
            ->values();
        $this->murs = $context->ressource()->enveloppe()->murs()
            ->map(fn(Mur $item) => new MurInput($context, $item))
            ->values();
        $this->planchers_bas = $context->ressource()->enveloppe()->planchers_bas()
            ->map(fn(PlancherBas $item) => new PlancherBasInput($context, $item))
            ->values();
        $this->planchers_hauts = $context->ressource()->enveloppe()->planchers_hauts()
            ->map(fn(PlancherHaut $item) => new PlancherHautInput($context, $item))
            ->values();
        $this->baies = $context->ressource()->enveloppe()->baies()
            ->map(fn(Baie $item) => new BaieInput($context, $item))
            ->values();
        $this->portes = $context->ressource()->enveloppe()->portes()
            ->map(fn(Porte $item) => new PorteInput($context, $item))
            ->values();
        $this->ponts_thermiques = $context->ressource()->enveloppe()->ponts_thermiques()
            ->map(fn(PontThermique $item) => new PontThermiqueInput($context, $item))
            ->values();
    }

    /**
     * @return ParoiInput[]
     */
    public function parois(): array
    {
        return array_merge(
            $this->murs,
            $this->planchers_bas,
            $this->planchers_hauts,
            $this->baies,
            $this->portes,
        );
    }

    public function q4pa_conv(): ?float
    {
        return $this->context->ressource()->enveloppe()->q4pa_conv();
    }

    public function exposition(): Exposition
    {
        return $this->context->ressource()->enveloppe()->exposition();
    }

    public function presence_brasseurs_air(): bool
    {
        return $this->context->ressource()->enveloppe()->presence_brasseurs_air();
    }

    // * Données calculées

    public function inertie_rule(): InertieEnveloppeRule
    {
        return $this->require(InertieEnveloppeRule::class);
    }

    public function deperdition_rule(): DeperditionEnveloppeRule
    {
        return $this->require(DeperditionEnveloppeRule::class);
    }

    public function deperdition_ventilation_rule(): DeperditionRenouvellementAirRule
    {
        return $this->require(DeperditionRenouvellementAirRule::class);
    }

    public function apport_rule(): ApportEnveloppeRule
    {
        return $this->require(ApportEnveloppeRule::class);
    }

    public function inertie(): Inertie
    {
        return $this->inertie_rule()->inertie();
    }

    public function gv(): float
    {
        return $this->deperdition_rule()->gv();
    }

    public function dr(): float
    {
        return $this->deperdition_ventilation_rule()->dr();
    }

    public function f(Mois $mois): float
    {
        return $this->apport_rule()->f($mois);
    }

    public function apport(?Mois $mois = null): float
    {
        return $mois ? $this->apport_rule()->apport_j($mois) : $this->apport_rule()->apport();
    }

    public function apport_fr(?Mois $mois = null): float
    {
        return $mois ? $this->apport_rule()->apport_fr_j($mois) : $this->apport_rule()->apport_fr();
    }
}
