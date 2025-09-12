<?php

namespace App\Tests\Engine\Performance;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};
use App\Engine\Performance\Ventilation\ConsommationVentilation;
use App\Engine\Performance\Ventilation\DimensionnementInstallation;
use App\Engine\Performance\Ventilation\DimensionnementSysteme;
use App\Engine\Performance\Ventilation\PuissanceAuxiliaire;

final class VentilationTest extends RuleTestCase
{
    public static function filepath(): string
    {
        return __DIR__ . '/ventilation.yaml';
    }

    public static function registre(): array
    {
        return [
            'rdim_installation' => DimensionnementInstallation::class,
            'rdim_systeme' => DimensionnementSysteme::class,
            'pvent_moy' => PuissanceAuxiliaire::class,
            'caux' => ConsommationVentilation::class,
        ];
    }

    protected static function dataNormalizer(array $input): array
    {
        self::normalize($input, ['type_ventilation'], fn($value) => $value ? TypeVentilation::from($value) : null);
        self::normalize($input, ['type_generateur'], fn($value) => $value ? TypeGenerateur::from($value) : null);
        self::normalize($input, ['type_vmc'], fn($value) => $value ? TypeVmc::from($value) : null);
        return parent::dataNormalizer($input);
    }
}
