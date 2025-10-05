<?php

namespace App\Dto\Ressource;

use App\Domain\Ressource\Ressource;
use App\Dto\Adresse\AdresseDto;
use App\Dto\Batiment\BatimentDto;
use App\Dto\Chauffage\ChauffageDto;
use App\Dto\Ecs\EcsDto;
use App\Dto\Enveloppe\EnveloppeDto;
use App\Dto\Logement\LogementDto;
use App\Dto\Production\ProductionDto;
use App\Dto\Refroidissement\RefroidissementDto;
use App\Dto\Ventilation\VentilationDto;
use Symfony\Component\Validator\Constraints;

/**
 * @property array<LogementDto>
 */
final class RessourceDto
{
    public function __construct(
        public ?string $id,
        public \DateTimeImmutable $date_visite,
        public \DateTimeImmutable $date_etablissement,
        #[Constraints\Valid]
        public AdresseDto $adresse,
        #[Constraints\Valid]
        public BatimentDto $batiment,
        #[Constraints\Valid]
        public EnveloppeDto $enveloppe,
        #[Constraints\Valid]
        public ChauffageDto $chauffage,
        #[Constraints\Valid]
        public EcsDto $ecs,
        #[Constraints\Valid]
        public RefroidissementDto $refroidissement,
        #[Constraints\Valid]
        public VentilationDto $ventilation,
        #[Constraints\Valid]
        public ProductionDto $production,
        #[Constraints\All([new Constraints\Type(LogementDto::class)])]
        #[Constraints\Valid]
        public array $logements,
    ) {}

    public static function from(Ressource $data): self
    {
        return new self(
            id: (string) $data->id(),
            date_visite: $data->date_visite(),
            date_etablissement: $data->date_etablissement(),
            adresse: AdresseDto::from($data->adresse()),
            batiment: BatimentDto::from($data->batiment()),
            enveloppe: EnveloppeDto::from($data->enveloppe()),
            chauffage: ChauffageDto::from($data->chauffage()),
            ecs: EcsDto::from($data->ecs()),
            refroidissement: RefroidissementDto::from($data->refroidissement()),
            ventilation: VentilationDto::from($data->ventilation()),
            production: ProductionDto::from($data->production()),
            logements: LogementDto::fromCollection($data->logements()),
        );
    }

    #[Constraints\IsTrue]
    public function is_generateur_mixte_exists(): bool
    {
        foreach ($this->chauffage->generateurs as $generateur) {
            if ($generateur->position->generateur_mixte_id) {
                if (null === array_find($this->ecs->generateurs, fn($item) => $item->id === $generateur->position->generateur_mixte_id)) {
                    return false;
                }
            }
        }
        foreach ($this->ecs->generateurs as $generateur) {
            if ($generateur->position->generateur_mixte_id) {
                if (null === array_find($this->chauffage->generateurs, fn($item) => $item->id === $generateur->position->generateur_mixte_id)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function __normalize(): array
    {
        return [
            'id' => $this->id,
            'date_visite' => $this->date_visite->format('Y-m-d'),
            'date_etablissement' => $this->date_etablissement->format('Y-m-d'),
            'adresse' => $this->adresse->__normalize(),
            'batiment' => $this->batiment->__normalize(),
            'enveloppe' => $this->enveloppe->__normalize(),
            'chauffage' => $this->chauffage->__normalize(),
            'ecs' => $this->ecs->__normalize(),
            'refroidissement' => $this->refroidissement->__normalize(),
            'ventilation' => $this->ventilation->__normalize(),
            'production' => $this->production->__normalize(),
            'logements' => array_map(fn(LogementDto $logement) => $logement->__normalize(), $this->logements),
        ];
    }
}
