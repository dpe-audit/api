<?php

namespace App\Dto\Refroidissement;

use App\Domain\Refroidissement\Installation\{Installation, InstallationData};

/**
 * @see https://github.com/dpe-audit/schemas/blob/main/schemas/refroidissement/installation.yaml
 */
final class InstallationDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly float $surface,
        public readonly ?InstallationData $data = null,
    ) {}

    public static function from(Installation $entity): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            data: $entity->data(),
        );
    }

    public function __normalize(): array
    {
        $data = [
            'id' => $this->id,
            'description' => $this->description,
            'surface' => $this->surface,
        ];
        if ($this->data) {
            $data['data'] = [
                'rdim' => $this->data->rdim,
            ];
        }
        return $data;
    }
}
