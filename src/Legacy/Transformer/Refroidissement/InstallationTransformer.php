<?php

namespace App\Legacy\Transformer\Refroidissement;

use App\Dto\Refroidissement\InstallationDto;
use App\Legacy\Model\Climatisation;
use App\Legacy\Transformer\Context;

final class InstallationTransformer
{
    public function __invoke(Climatisation $climatisation, Context $context): InstallationDto
    {
        return new InstallationDto(
            id: $climatisation->id(),
            description: $climatisation->description(),
            surface: $climatisation->surface_clim,
        );
    }
}
