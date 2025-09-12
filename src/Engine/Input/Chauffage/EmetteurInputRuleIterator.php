<?php

namespace App\Engine\Input\Chauffage;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<EmetteurInput>
 */
abstract class EmetteurInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->chauffage->emetteurs;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
