<?php

namespace App\Domain\Enveloppe\Baie\Position;

use App\Domain\Enveloppe\DoubleFenetre\DoubleFenetre;
use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Domain\Enveloppe\Paroi\Paroi;

final class Position
{
    public function __construct(
        public readonly float $surface,
        public readonly Mitoyennete $mitoyennete,
        public readonly float $inclinaison,
        public readonly ?float $orientation,
        public readonly ?TypePose $type_pose,
        public readonly ?bool $presence_soubassement,
        public readonly ?Paroi $paroi,
        public readonly ?Lnc $local_non_chauffe,
        public readonly ?DoubleFenetre $double_fenetre,
    ) {}

    public static function create(
        float $surface,
        Mitoyennete $mitoyennete,
        float $inclinaison,
        ?float $orientation,
        ?TypePose $type_pose,
        ?bool $presence_soubassement,
        ?Paroi $paroi,
        ?Lnc $local_non_chauffe,
        ?DoubleFenetre $double_fenetre,
    ): self {
        return new self(
            surface: $surface,
            mitoyennete: $mitoyennete,
            inclinaison: $inclinaison,
            orientation: $orientation,
            type_pose: $type_pose,
            presence_soubassement: $presence_soubassement,
            paroi: $paroi,
            local_non_chauffe: $local_non_chauffe,
            double_fenetre: $double_fenetre,
        );
    }
}
