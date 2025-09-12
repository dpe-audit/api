<?php

namespace App\Api\Eclairage\Model;

use App\Model\Eclairage\Eclairage as Entity;

final class Eclairage
{
    public function __construct(
        public string $id,

        public ?EclairageData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            data: EclairageData::from($entity),
        );
    }
}
