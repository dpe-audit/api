<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Systeme\{Systeme, SystemeData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/refroidissement/systeme.yaml
 */
final class SystemeDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $installation_id,
        public readonly string $generateur_id,
        public readonly ?SystemeData $data = null,
    ) {}

    public static function from(Systeme $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            installation_id: (string) $entity->installation()->id(),
            generateur_id: (string) $entity->generateur()->id(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'installation_id' => $this->installation_id,
            'generateur_id' => $this->generateur_id,
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
                'cef_fr' => $this->data->cef_fr,
                'cep_fr' => $this->data->cep_fr,
                'eges_fr' => $this->data->eges_fr,
                'cef_aux' => $this->data->cef_aux,
                'cep_aux' => $this->data->cep_aux,
                'eges_aux' => $this->data->eges_aux,
            ];
        }
        return $data;
    }
}
