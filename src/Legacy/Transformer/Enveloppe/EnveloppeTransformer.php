<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Exposition;
use App\Dto\Enveloppe\Baie\BaieDto;
use App\Dto\Enveloppe\EnveloppeDto;
use App\Dto\Enveloppe\DoubleFenetre\DoubleFenetreDto;
use App\Dto\Enveloppe\Lnc\LncDto;
use App\Dto\Enveloppe\Masque\MasqueDto;
use App\Dto\Enveloppe\Niveau\NiveauDto;
use App\Dto\Enveloppe\PlancherBas\PlancherBasDto;
use App\Dto\Enveloppe\PlancherHaut\PlancherHautDto;
use App\Dto\Enveloppe\PontThermique\PontThermiqueDto;
use App\Dto\Enveloppe\Porte\PorteDto;
use App\Legacy\Transformer\Context;

final class EnveloppeTransformer
{
    private Context $context;

    public function __construct(
        private BaieTransformer $baie_transformer,
        private DoubleFenetreTransformer $double_fenetre_transformer,
        private BaieDoubleFenetreTransformer $double_fenetre_baie_transformer,
        private EtsTransformer $ets_transformer,
        private LncTransformer $lnc_transformer,
        private MasqueProcheTransformer $masque_proche_transformer,
        private MasqueLointainHomogeneTransformer $masque_lointain_homogene_transformer,
        private MasqueLointainNonHomogeneTransformer $masque_lointain_non_homogene_transformer,
        private MurTransformer $mur_transformer,
        private NiveauTransformer $niveau_transformer,
        private PlancherBasTransformer $plancher_bas_transformer,
        private PlancherHautTransformer $plancher_haut_transformer,
        private PontThermiqueTransformer $pont_thermique_transformer,
        private PorteTransformer $porte_transformer,
    ) {}


    public function exposition(): Exposition
    {
        foreach ($this->context->ressource()->logement()->ventilation_collection as $ventilation) {
            return match ($ventilation->plusieurs_facade_exposee) {
                true => Exposition::EXPOSITION_MULTIPLE,
                false => Exposition::EXPOSITION_SIMPLE,
            };
        }
        return Exposition::EXPOSITION_MULTIPLE;
    }

    public function q4pa_conv(): ?float
    {
        foreach ($this->context->ressource()->logement()->ventilation_collection as $ventilation) {
            if ($ventilation->q4pa_conv_saisi > 0) {
                return $ventilation->q4pa_conv_saisi;
            }
        }
        return null;
    }

    public function presence_brasseurs_air(): float
    {
        return $this->context->ressource()->logement()->sortie->confort_ete->presence_brasseurs_air() ?? false;
    }

    /**
     * @return NiveauDto[]
     */
    public function niveaux(): array
    {
        $collection = [];
        $collection[] = $this->niveau_transformer->__invoke($this->context);
        return $collection;
    }

    /**
     * @return LncDto[]
     */
    public function locaux_non_chauffes(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->parois() as $paroi) {
            $collection[] = $this->lnc_transformer->__invoke($paroi, $this->context);
        }
        foreach ($this->context->logement()->enveloppe->ets_collection as $ets) {
            $collection[] = $this->ets_transformer->__invoke($ets, $this->context);
        }
        return array_filter($collection);
    }

    /**
     * @return array<MasqueDto>
     */
    public function masques(): array
    {
        $collection = [];

        foreach ($this->context->logement()->enveloppe->baie_vitree_collection as $baie_vitree) {
            $collection[] = $this->masque_proche_transformer->__invoke($baie_vitree);
            $collection[] = $this->masque_lointain_homogene_transformer->__invoke($baie_vitree);

            foreach ($baie_vitree->masque_lointain_non_homogene_collection as $masque_lointain_non_homogene) {
                $collection[] = $this->masque_lointain_non_homogene_transformer->__invoke($masque_lointain_non_homogene);
            }
        }
        return array_filter($collection);
    }

    /**
     * @return array<DoubleFenetreDto>
     */
    public function doubles_fenetres(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->baie_vitree_collection as $baie_vitree) {
            $collection[] = $baie_vitree->baie_vitree_double_fenetre
                ? $this->double_fenetre_transformer->__invoke($baie_vitree->baie_vitree_double_fenetre)
                : $this->double_fenetre_baie_transformer->__invoke($baie_vitree);
        }
        return array_filter($collection);
    }

    /**
     * @return array<BaieDto>
     */
    public function baies(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->baie_vitree_collection as $paroi) {
            $collection[] = $this->baie_transformer->__invoke($paroi, $this->context);
        }
        return array_filter($collection);
    }

    /**
     * @return array<MurDto>
     */
    public function murs(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->mur_collection as $paroi) {
            $collection[] = $this->mur_transformer->__invoke($paroi, $this->context);
        }
        return array_filter($collection);
    }

    /**
     * @return array<PlancherBasDto>
     */
    public function planchers_bas(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->plancher_bas_collection as $paroi) {
            $collection[] = $this->plancher_bas_transformer->__invoke($paroi, $this->context);
        }
        return array_filter($collection);
    }

    /**
     * @return array<PlancherHautDto>
     */
    public function planchers_hauts(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->plancher_haut_collection as $paroi) {
            $collection[] = $this->plancher_haut_transformer->__invoke($paroi, $this->context);
        }
        return array_filter($collection);
    }

    /**
     * @return array<PorteDto>
     */
    public function portes(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->porte_collection as $paroi) {
            $collection[] = $this->porte_transformer->__invoke($paroi, $this->context);
        }
        return array_filter($collection);
    }

    /**
     * @return array<PontThermiqueDto>
     */
    public function ponts_thermiques(): array
    {
        $collection = [];
        foreach ($this->context->logement()->enveloppe->pont_thermique_collection as $pont_thermique) {
            $collection[] = $this->pont_thermique_transformer->__invoke($pont_thermique, $this->context);
        }
        return array_filter($collection);
    }

    public function __invoke(Context $context): EnveloppeDto
    {
        $this->context = $context;

        return new EnveloppeDto(
            exposition: $this->exposition(),
            q4pa_conv: $this->q4pa_conv(),
            presence_brasseurs_air: $this->presence_brasseurs_air(),
            baies: $this->baies(),
            doubles_fenetres: $this->doubles_fenetres(),
            locaux_non_chauffes: $this->locaux_non_chauffes(),
            masques: $this->masques(),
            murs: $this->murs(),
            niveaux: $this->niveaux(),
            planchers_bas: $this->planchers_bas(),
            planchers_hauts: $this->planchers_hauts(),
            ponts_thermiques: $this->ponts_thermiques(),
            portes: $this->portes(),
        );
    }
}
