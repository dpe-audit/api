<?php

namespace App\Dto\Enveloppe;

use App\Domain\Enveloppe\{Enveloppe, Exposition};
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
    ) {}

    public static function from(Enveloppe $data): self
    {
        return new self(
            exposition: $data->exposition(),
            q4pa_conv: $data->q4pa_conv(),
            presence_brasseurs_air: $data->presence_brasseurs_air(),
            niveaux: $data->niveaux()->map(fn($item) => NiveauDto::from($item))->values(),
            locaux_non_chauffes: $data->locaux_non_chauffes()->map(fn($item) => LncDto::from($item))->values(),
            doubles_fenetres: $data->doubles_fenetres()->map(fn($item) => DoubleFenetreDto::from($item))->values(),
            masques: $data->masques()->map(fn($item) => MasqueDto::from($item))->values(),
            baies: $data->baies()->map(fn($item) => BaieDto::from($item))->values(),
            murs: $data->murs()->map(fn($item) => MurDto::from($item))->values(),
            planchers_bas: $data->planchers_bas()->map(fn($item) => PlancherBasDto::from($item))->values(),
            planchers_hauts: $data->planchers_hauts()->map(fn($item) => PlancherHautDto::from($item))->values(),
            portes: $data->portes()->map(fn($item) => PorteDto::from($item))->values(),
            ponts_thermiques: $data->ponts_thermiques()->map(fn($item) => PontThermiqueDto::from($item))->values(),
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
        return [
            'exposition' => $this->exposition->value,
            'q4pa_conv' => $this->q4pa_conv,
            'presence_brasseurs_air' => $this->presence_brasseurs_air,
            'niveaux' => array_map(fn($dto) => $dto->__normalize(), $this->niveaux),
            'locaux_non_chauffes' => array_map(fn($dto) => $dto->__normalize(), $this->locaux_non_chauffes),
            'doubles_fenetres' => array_map(fn($dto) => $dto->__normalize(), $this->doubles_fenetres),
            'masques' => array_map(fn($dto) => $dto->__normalize(), $this->masques),
            'baies' => array_map(fn($dto) => $dto->__normalize(), $this->baies),
            'murs' => array_map(fn($dto) => $dto->__normalize(), $this->murs),
            'planchers_bas' => array_map(fn($dto) => $dto->__normalize(), $this->planchers_bas),
            'planchers_hauts' => array_map(fn($dto) => $dto->__normalize(), $this->planchers_hauts),
            'portes' => array_map(fn($dto) => $dto->__normalize(), $this->portes),
            'ponts_thermiques' => array_map(fn($dto) => $dto->__normalize(), $this->ponts_thermiques),
        ];
    }
}
