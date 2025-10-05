<?php

namespace App\Engine\Rules\Apport\FacteurEnsoleillement;

use App\Engine\Input\Enveloppe\BaieInputRuleIterator;

final class FacteurEnsoleillementBaieRule extends BaieInputRuleIterator
{
    /**
     * Facteur d'ensoleillement de la baie
     */
    public function fe(): float
    {
        return $this->get('fe', function (): float {
            return $this->fe1() * $this->fe2();
        });
    }

    /**
     * Facteur d'ensoleillement lié aux masques proches
     */
    public function fe1(): float
    {
        return $this->get('fe1', function (): float {
            $fe1 = 1;
            foreach ($this->item()->masques() as $item) {
                if (null !== $item->fe1()) {
                    $fe1 = min($fe1, $item->fe1());
                }
            }
            return static::round($fe1);
        });
    }
    /**
     * Facteur d'ensoleillement lié aux masques lointains
     */
    public function fe2(): float
    {
        return $this->get('fe2', function (): float {
            $fe2 = 1;
            $omb = 0;

            foreach ($this->item()->masques() as $item) {
                $fe2 = min($fe2, $item->fe2() ?? 1);
                $omb += $item->omb() ?? 0;
            }
            $fe2 = min($fe2, 1 - min($omb, 100) / 100);
            return static::round($fe2);
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            fe: $this->fe(),
        ));
    }
}
