<?php

namespace App\Domain\Chauffage\Installation\Solaire;

enum Usage: string
{
    case CHAUFFAGE = 'chauffage';
    case CHAUFFAGE_ECS = 'chauffage_ecs';

    public static function from_enum_usage_generateur_id(int $id): self
    {
        return match ($id) {
            1 => self::CHAUFFAGE,
            3 => self::CHAUFFAGE_ECS,
        };
    }
}
