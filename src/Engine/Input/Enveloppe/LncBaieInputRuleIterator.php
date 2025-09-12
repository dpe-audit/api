<?php

namespace App\Engine\Input\Enveloppe;

use App\Domain\Enveloppe\Lnc\TypeLnc;
use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<LncBaieInput>
 */
abstract class LncBaieInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        $collection = [];

        foreach ($this->data()->enveloppe->locaux_non_chauffes as $local_non_chauffe) {
            if ($local_non_chauffe->type() === TypeLnc::ESPACE_TAMPON_SOLARISE) {
                continue;
            }
            $collection = array_merge($collection, $local_non_chauffe->baies);
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
