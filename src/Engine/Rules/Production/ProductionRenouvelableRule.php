<?php

namespace App\Engine\Rules\Production;

use App\Engine\{Context, Rule};

final class ProductionRenouvelableRule extends Rule
{
    /**
     * Production photovoltaïque en kWh/an
     */
    public function ppv(): float
    {
        return $this->get('ppv', function (): float {
            return $this->input()->production->panneaux_photovoltaiques()
                ->map(fn($item) => $this->requireIterator(ProductionPhotovoltaiqueRule::class, $item)->ppv())
                ->reduce(fn(float $carry, float $item) => $carry += $item);
        });
    }

    /**
     * @inheritDoc
     */
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        $context->input()->production->calcule($context->input()->production->data()->with(
            ppv: $this->ppv(),
        ));
    }
}
