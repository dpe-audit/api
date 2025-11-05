<?php

namespace App\Legacy\Transformer\Refroidissement;

use App\Dto\Refroidissement\SystemeDto;
use App\Legacy\Model\Climatisation;
use App\Legacy\Transformer\Context;

final class SystemeTransformer
{
    public function __invoke(Climatisation $climatisation, Context $context): SystemeDto
    {
        return new SystemeDto(
            id: $climatisation->id(),
            description: $climatisation->description(),
            installation_id: $climatisation->id(),
            generateur_id: $climatisation->id(),
        );
    }
}
