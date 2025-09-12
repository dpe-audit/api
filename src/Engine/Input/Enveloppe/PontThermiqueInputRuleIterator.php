<?php

namespace App\Engine\Input\Enveloppe;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<PontThermiqueInput>
 */
abstract class PontThermiqueInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->enveloppe->ponts_thermiques;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
