<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Lnc\Baie\Mitoyennete;
use App\Engine\Input\Enveloppe\LncBaieInputRuleIterator;

final class DeperditionLncBaieRule extends LncBaieInputRuleIterator
{
    /**
     * Surface donnant sur l'extérieur
     */
    public function aue(): float
    {
        return $this->get('aue', function (): float {
            return match ($this->item()->mitoyennete()) {
                Mitoyennete::EXTERIEUR, Mitoyennete::ENTERRE, Mitoyennete::TERRE_PLEIN => $this->item()->surface(),
                default => 0
            };
        });
    }

    /**
     * Surface donnant sur un espace chauffé
     */
    public function aiu(): float
    {
        return $this->get('aiu', function (): float {
            return match ($this->item()->mitoyennete()) {
                Mitoyennete::LOCAL_CHAUFFE => $this->item()->surface(),
                default => 0
            };
        });
    }
}
