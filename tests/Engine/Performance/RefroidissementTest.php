<?php

namespace App\Tests\Engine\Performance;

use App\Engine\Performance\Refroidissement\DimensionnementInstallation;
use App\Engine\Performance\Refroidissement\DimensionnementSysteme;
use App\Engine\Performance\Refroidissement\PerformanceGenerateur;

final class RefroidissementTest extends RuleTestCase
{
    public static function filepath(): string
    {
        return __DIR__ . '/refroidissement.yaml';
    }

    public static function registre(): array
    {
        return [
            'rdim_installation' => DimensionnementInstallation::class,
            'rdim_systeme' => DimensionnementSysteme::class,
            'eer' => PerformanceGenerateur::class,
        ];
    }
}
