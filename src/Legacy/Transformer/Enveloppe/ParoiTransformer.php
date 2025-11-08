<?php

namespace App\Legacy\Transformer\Enveloppe;

use App\Domain\Enveloppe\Paroi\Mitoyennete;
use App\Legacy\Model\Paroi;
use App\Legacy\Transformer\Context;

/**
 * @template T of Paroi
 */
abstract class ParoiTransformer
{
    /**
     * @var T
     */
    protected Paroi $paroi;

    protected Context $context;

    public function mitoyennete(): Mitoyennete
    {
        return match ($this->paroi->enum_type_adjacence_id) {
            1 => Mitoyennete::EXTERIEUR,
            2 => Mitoyennete::ENTERRE,
            3 => Mitoyennete::VIDE_SANITAIRE,
            4 => Mitoyennete::LOCAL_NON_RESIDENTIEL,
            5 => Mitoyennete::TERRE_PLEIN,
            6 => Mitoyennete::SOUS_SOL_NON_CHAUFFE,
            7 => Mitoyennete::LOCAL_NON_ACCESSIBLE,
            20 => Mitoyennete::LOCAL_NON_RESIDENTIEL,
            22 => Mitoyennete::LOCAL_RESIDENTIEL,
            8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21 => match (true) {
                $this->paroi->surface_aue > 0 => Mitoyennete::LOCAL_NON_CHAUFFE,
                default => Mitoyennete::LOCAL_NON_ACCESSIBLE,
            },
        };
    }

    public function local_non_chauffe_id(): ?string
    {
        if ($this->mitoyennete() !== Mitoyennete::LOCAL_NON_CHAUFFE) {
            return null;
        }
        if ($this->paroi->enum_type_adjacence_id === 10) {
            if ($this->paroi->reference_lnc) {
                if ($id = $this->context->logement()->enveloppe->match_ets($this->paroi->reference_lnc)?->id()) {
                    return $id;
                }
            }
            foreach ($this->context->logement()->enveloppe->ets_collection as $ets) {
                if ($ets->enum_cfg_isolation_lnc_id !== $this->paroi->enum_cfg_isolation_lnc_id) {
                    continue;
                }
                if ($ets->tv_coef_reduction_deperdition_id !== $this->paroi->tv_coef_reduction_deperdition_id) {
                    continue;
                }
                return $ets->id();
            }
            foreach ($this->context->logement()->enveloppe->ets_collection as $ets) {
                if ($ets->enum_cfg_isolation_lnc_id === $this->paroi->enum_cfg_isolation_lnc_id) {
                    return $ets->id();
                }
                if ($ets->tv_coef_reduction_deperdition_id === $this->paroi->tv_coef_reduction_deperdition_id) {
                    return $ets->id();
                }
            }
        }
        return $this->paroi->id();
    }
}
