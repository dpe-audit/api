<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Lnc\Paroi\{Paroi, Mitoyennete, Isolation};
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<Paroi>
 */
final class DeperditionLncParoiRule extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        $collection = [];
        foreach ($this->input()->enveloppe->locaux_non_chauffes() as $item) {
            $collection = array_merge($collection, $item->parois()->values());
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
        return $this->item()->isolation() === Isolation::ISOLE ?? false;
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
