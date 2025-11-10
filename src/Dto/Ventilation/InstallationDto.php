<?php

namespace App\Dto\Ventilation;

use App\Domain\Ventilation\Installation\{Installation, InstallationData, TypeVentilation};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/ventilation/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly TypeVentilation $type,
        public readonly ?string $generateur_id,
        public readonly ?InstallationData $data = null,
    ) {}

    public static function from(Installation $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            type: $entity->type(),
            generateur_id: $entity->generateur() ? (string) $entity->generateur()->id() : null,
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
            'type' => $this->type->value,
            'generateur_id' => $this->generateur_id,
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
                'qvarep_conv' => $this->data->qvarep_conv,
                'qvasouf_conv' => $this->data->qvasouf_conv,
                'smea_conv' => $this->data->smea_conv,
            ];
        }
        return $data;
    }
}
