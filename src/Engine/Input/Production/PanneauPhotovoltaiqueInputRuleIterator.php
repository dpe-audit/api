<?php

namespace App\Engine\Input\Production;

use App\Engine\RuleIterator;

/**
 * @extends RuleIterator<PanneauPhotovoltaiqueInput>
 */
abstract class PanneauPhotovoltaiqueInputRuleIterator extends RuleIterator
{
    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->data()->production->panneaux_photovoltaiques;
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->entity->id();
    }
}
