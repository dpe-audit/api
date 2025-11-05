<?php

namespace App\Legacy\Transformer\Ventilation;

use App\Domain\Ventilation\Installation\TypeVentilation;
use App\Dto\Ventilation\InstallationDto;
use App\Legacy\Model\Ventilation;
use App\Legacy\Transformer\Context;

final class InstallationTransformer
{
    private Ventilation $ventilation;

    public function __construct(private readonly GenerateurTransformer $generateurTransformer) {}

    public function type_ventilation(): TypeVentilation
    {
        return match ($this->ventilation->enum_type_ventilation_id) {
            1 => TypeVentilation::VENTILATION_NATURELLE_OUVERTURE_FENETRES,
            2 => TypeVentilation::VENTILATION_NATURELLE_ENTREES_AIR_HAUTES_BASSES,
            25 => TypeVentilation::VENTILATION_NATURELLE_CONDUIT,
            34 => TypeVentilation::VENTILATION_NATURELLE_CONDUIT_ENTREES_AIR_HYGROREGLABLES,
            default => TypeVentilation::VENTILATION_MECANIQUE,
        };
    }

    public function __invoke(Ventilation $ventilation, Context $context): InstallationDto
    {
        $this->ventilation = $ventilation;

        $generateur = $this->generateurTransformer->__invoke($ventilation, $context);

        return new InstallationDto(
            id: (string) $ventilation->id(),
            description: $ventilation->description(),
            surface: $ventilation->surface_ventile,
            type: $this->type_ventilation(),
            generateur_id: $generateur?->id,
        );
    }
}
