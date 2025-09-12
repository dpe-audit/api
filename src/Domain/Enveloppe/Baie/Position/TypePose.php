<?php

namespace App\Domain\Enveloppe\Baie\Position;

enum TypePose: string
{
    case NU_EXTERIEUR = 'nu_exterieur';
    case NU_INTERIEUR = 'nu_interieur';
    case TUNNEL = 'tunnel';

    public static function from_enum_type_pose_id(int $id): ?self
    {
        return match ($id) {
            1 => self::NU_EXTERIEUR,
            2 => self::NU_INTERIEUR,
            3 => self::TUNNEL,
            4 => null,
        };
    }
}
