<?php

namespace App\Dto\Chauffage\Systeme;

use App\Domain\Chauffage\Systeme\{Systeme, SystemeData};
use App\Domain\Chauffage\TypeChauffage;

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/chauffage/systeme.yaml
 * 
 * @property array<string> $emetteurs
 */
final class SystemeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $generateur_id,
        public readonly string $installation_id,
        public readonly TypeChauffage $type,
        public readonly ?int $cascade,
        public readonly ?ReseauDto $reseau,
        public readonly array $emetteurs,
        public readonly ?SystemeData $data = null,
    ) {}

    public static function from(Systeme $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            generateur_id: (string) $entity->generateur()->id(),
            installation_id: (string) $entity->installation()->id(),
            type: $entity->type(),
            cascade: $entity->cascade(),
            reseau: $entity->reseau() ? ReseauDto::from($entity->reseau()) : null,
            emetteurs: $entity->emetteurs()->map(fn($item) => (string) $item->id())->values(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'generateur_id' => $this->generateur_id,
            'installation_id' => $this->installation_id,
            'type' => $this->type->value,
            'cascade' => $this->cascade,
            'reseau' => $this->reseau?->__normalize(),
            'emetteurs' => array_values($this->emetteurs),
        ];
        if ($this->data) {
            $data['data'] = [
                'configuration' => $this->data->configuration,
                'rdim' => $this->data->rdim,
                'i0' => $this->data->i0,
                'int' => $this->data->int,
                'ich' => $this->data->ich,
                're' => $this->data->re,
                'rd' => $this->data->rd,
                'rg' => $this->data->rg,
                'rr' => $this->data->rr,
                'cef_ch' => $this->data->cef_ch,
                'cep_ch' => $this->data->cep_ch,
                'eges_ch' => $this->data->eges_ch,
                'cef_aux' => $this->data->cef_aux,
                'cep_aux' => $this->data->cep_aux,
                'eges_aux' => $this->data->eges_aux,
            ];
        }
        return $data;
    }
}
