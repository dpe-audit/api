<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Lnc\Baie\{Baie, Mitoyennete};
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Baie>
 */
final class DeperditionLncBaieRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        $collection = [];
        foreach ($this->input()->enveloppe->locaux_non_chauffes() as $item) {
            $collection = array_merge($collection, $item->baies()->values());
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function surface(): float
    {
        return $this->item()->position()->surface;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->item()->position()->mitoyennete;
    }

    public function isolation(): bool
    {
        return $this->item()->type_vitrage()->isolation();
    }

    // * Données calculées

    /**
     * Surface donnant sur l'extérieur
     */
    public function aue(): float
    {
        return $this->get('aue', function (): float {
            return match ($this->mitoyennete()) {
                Mitoyennete::EXTERIEUR, Mitoyennete::ENTERRE, Mitoyennete::TERRE_PLEIN => $this->surface(),
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
            return match ($this->mitoyennete()) {
                Mitoyennete::LOCAL_CHAUFFE => $this->surface(),
                default => 0
            };
        });
    }
}
