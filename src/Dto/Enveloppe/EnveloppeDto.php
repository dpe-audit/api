<?php

namespace App\Dto\Enveloppe;

use App\Domain\Enveloppe\{Enveloppe, EnveloppeData, Exposition};
use App\Dto\Enveloppe\Baie\BaieDto;
use App\Dto\Enveloppe\DoubleFenetre\DoubleFenetreDto;
use App\Dto\Enveloppe\Lnc\LncDto;
use App\Dto\Enveloppe\Masque\MasqueDto;
use App\Dto\Enveloppe\Mur\MurDto;
use App\Dto\Enveloppe\Niveau\NiveauDto;
use App\Dto\Enveloppe\PlancherBas\PlancherBasDto;
use App\Dto\Enveloppe\PlancherHaut\PlancherHautDto;
use App\Dto\Enveloppe\PontThermique\PontThermiqueDto;
use App\Dto\Enveloppe\Porte\PorteDto;
use App\Validation;
use Symfony\Component\Validator\Constraints;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/enveloppe/enveloppe.yaml
 * 
 * @property array<BaieDto> $baies
 * @property array<DoubleFenetreDto> $doubles_fenetres
 * @property array<LncDto> $locaux_non_chauffes
 * @property array<MasqueDto> $masques
 * @property array<MurDto> $murs
 * @property array<NiveauDto> $niveaux
 * @property array<PlancherBasDto> $planchers_bas
 * @property array<PlancherHautDto> $planchers_hauts
 * @property array<PorteDto> $portes
 * @property array<PontThermiqueDto> $ponts_thermiques
 */
#[Validation\Enveloppe\EnveloppeValid]
final class EnveloppeDto
{
    public function __construct(
        public Exposition $exposition,
        public ?float $q4pa_conv,
        public bool $presence_brasseurs_air,
        #[Constraints\All([new Constraints\Type(NiveauDto::class)])]
        #[Constraints\Valid]
        public array $niveaux,
        #[Constraints\All([new Constraints\Type(LncDto::class)])]
        #[Constraints\Valid]
        public array $locaux_non_chauffes,
        #[Constraints\All([new Constraints\Type(DoubleFenetreDto::class)])]
        #[Constraints\Valid]
        public array $doubles_fenetres,
        #[Constraints\All([new Constraints\Type(MasqueDto::class)])]
        #[Constraints\Valid]
        public array $masques,
        #[Constraints\All([new Constraints\Type(BaieDto::class)])]
        #[Constraints\Valid]
        public array $baies,
        #[Constraints\All([new Constraints\Type(MurDto::class)])]
        #[Constraints\Valid]
        public array $murs,
        #[Constraints\All([new Constraints\Type(PlancherBasDto::class)])]
        #[Constraints\Valid]
        public array $planchers_bas,
        #[Constraints\All([new Constraints\Type(PlancherHautDto::class)])]
        #[Constraints\Valid]
        public array $planchers_hauts,
        #[Constraints\All([new Constraints\Type(PorteDto::class)])]
        #[Constraints\Valid]
        public array $portes,
        #[Constraints\All([new Constraints\Type(PontThermiqueDto::class)])]
        #[Constraints\Valid]
        public array $ponts_thermiques,

        public readonly ?EnveloppeData $data = null,
    ) {}

    public static function from(Enveloppe $entity): self
    {
        return new self(
            exposition: $entity->exposition(),
            q4pa_conv: $entity->q4pa_conv(),
            presence_brasseurs_air: $entity->presence_brasseurs_air(),
            niveaux: $entity->niveaux()->map(fn($item) => NiveauDto::from($item))->values(),
            locaux_non_chauffes: $entity->locaux_non_chauffes()->map(fn($item) => LncDto::from($item))->values(),
            doubles_fenetres: $entity->doubles_fenetres()->map(fn($item) => DoubleFenetreDto::from($item))->values(),
            masques: $entity->masques()->map(fn($item) => MasqueDto::from($item))->values(),
            baies: $entity->baies()->map(fn($item) => BaieDto::from($item))->values(),
            murs: $entity->murs()->map(fn($item) => MurDto::from($item))->values(),
            planchers_bas: $entity->planchers_bas()->map(fn($item) => PlancherBasDto::from($item))->values(),
            planchers_hauts: $entity->planchers_hauts()->map(fn($item) => PlancherHautDto::from($item))->values(),
            portes: $entity->portes()->map(fn($item) => PorteDto::from($item))->values(),
            ponts_thermiques: $entity->ponts_thermiques()->map(fn($item) => PontThermiqueDto::from($item))->values(),
            data: $entity->data(),
        );
    }

    public function find_baie(string $id): ?BaieDto
    {
        return array_find($this->baies, fn($item) => $item->id === $id);
    }

    public function find_mur(string $id): ?MurDto
    {
        return array_find($this->murs, fn($item) => $item->id === $id);
    }

    public function find_plancher_bas(string $id): ?PlancherBasDto
    {
        return array_find($this->planchers_bas, fn($item) => $item->id === $id);
    }

    public function find_plancher_haut(string $id): ?PlancherHautDto
    {
        return array_find($this->planchers_hauts, fn($item) => $item->id === $id);
    }

    public function find_porte(string $id): ?PorteDto
    {
        return array_find($this->portes, fn($item) => $item->id === $id);
    }

    public function find_masque(string $id): ?MasqueDto
    {
        return array_find($this->masques, fn($item) => $item->id === $id);
    }

    public function find_local_non_chauffe(string $id): ?LncDto
    {
        return array_find($this->locaux_non_chauffes, fn($item) => $item->id === $id);
    }

    public function find_double_fenetre(string $id): ?DoubleFenetreDto
    {
        return array_find($this->doubles_fenetres, fn($item) => $item->id === $id);
    }

    public function __normalize(): array
    {
        $data = [
            'exposition' => $this->exposition->value,
            'q4pa_conv' => $this->q4pa_conv,
            'presence_brasseurs_air' => $this->presence_brasseurs_air,
            'niveaux' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->niveaux)),
            'locaux_non_chauffes' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->locaux_non_chauffes)),
            'doubles_fenetres' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->doubles_fenetres)),
            'masques' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->masques)),
            'baies' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->baies)),
            'murs' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->murs)),
            'planchers_bas' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->planchers_bas)),
            'planchers_hauts' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->planchers_hauts)),
            'portes' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->portes)),
            'ponts_thermiques' => array_values(array_map(fn($dto) => $dto->__normalize(), $this->ponts_thermiques)),
        ];
        if ($this->data) {
            $data['data'] = [
                'inertie' => $this->data->inertie?->value,
                'permeabilite' => [
                    'hvent' => $this->data->permeabilite?->hvent,
                    'hperm' => $this->data->permeabilite?->hperm,
                    'q4pa_conv' => $this->data->permeabilite?->q4pa_conv,
                    'qvarep_conv' => $this->data->permeabilite?->qvarep_conv,
                    'qvasouf_conv' => $this->data->permeabilite?->qvasouf_conv,
                    'smea_conv' => $this->data->permeabilite?->smea_conv,
                ],
                'deperditions' => [
                    'gv' => $this->data->deperditions?->gv,
                    'dp' => $this->data->deperditions?->dp,
                    'dp_murs' => $this->data->deperditions?->dp_murs,
                    'dp_planchers_bas' => $this->data->deperditions?->dp_planchers_bas,
                    'dp_planchers_hauts' => $this->data->deperditions?->dp_planchers_hauts,
                    'dp_baies' => $this->data->deperditions?->dp_baies,
                    'dp_portes' => $this->data->deperditions?->dp_portes,
                    'pt' => $this->data->deperditions?->pt,
                    'dr' => $this->data->deperditions?->dr,
                    'ubat' => $this->data->deperditions?->ubat,
                    'performance' => $this->data->deperditions?->performance?->value,
                ],
                'confort_ete' => [
                    'performance' => $this->data->confort_ete?->performance?->value,
                    'inertie_lourde' => $this->data->confort_ete?->inertie_lourde,
                    'isolation_plancher_haut' => $this->data->confort_ete?->isolation_plancher_haut,
                    'presence_protection_solaire' => $this->data->confort_ete?->presence_protection_solaire,
                    'logement_traversant' => $this->data->confort_ete?->logement_traversant,
                    'presence_brasseur_air' => $this->data->confort_ete?->presence_brasseur_air,
                ],
                'apports' => [
                    'f' => $this->data->apports?->f,
                    'apport' => $this->data->apports?->apport,
                    'apport_interne' => $this->data->apports?->apport_interne,
                    'apport_solaire' => $this->data->apports?->apport_solaire,
                    'apport_fr' => $this->data->apports?->apport_fr,
                    'apport_interne_fr' => $this->data->apports?->apport_interne_fr,
                    'apport_solaire_fr' => $this->data->apports?->apport_solaire_fr,
                ]
            ];
        }
        return $data;
    }
}
