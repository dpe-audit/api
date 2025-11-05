<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Dto\Enveloppe\Lnc\LncDto;
use App\Legacy\Model\Ets;
use App\Legacy\Transformer\Context;

final class EtsTransformer
{
    public function __construct(private readonly EtsBaieTransformer $ets_baie_transformer) {}

    public function __invoke(Ets $ets, Context $context): LncDto
    {
        $baies = [];

        foreach ($ets->ets_baie_collection as $ets_baie) {
            $baies[] = $this->ets_baie_transformer->__invoke($ets_baie, $ets);
        }

        return new LncDto(
            id: $ets->id(),
            description: $ets->description(),
            type: TypeLnc::ESPACE_TAMPON_SOLARISE,
            parois: [],
            baies: array_filter($baies),
        );
    }
}
