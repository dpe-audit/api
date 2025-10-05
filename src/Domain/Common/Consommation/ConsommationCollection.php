<?php

namespace App\Domain\Common\Consommation;

use App\Domain\Common\Enum\{Energie, Usage};

/**
 * @property Consommation[] $elements
 */
final class ConsommationCollection
{
    public function __construct(private array $elements = []) {}

    public static function create(Consommation ...$elements): self
    {
        /** @var Consommation[] $unique */
        $unique = [];

        foreach ($elements as $element) {
            $key = $element->usage->value . '::' . $element->energie->value;

            if (isset($unique[$key])) {
                $unique[$key] = Consommation::create(
                    usage: $element->usage,
                    energie: $element->energie,
                    cef: $unique[$key]->cef + $element->cef,
                    cep: $unique[$key]->cep + $element->cep,
                    eges: $unique[$key]->eges + $element->eges,
                );
            } else {
                $unique[$key] = $element;
            }
        }
        return new self(array_values($unique));
    }

    public function with(Consommation $element): self
    {
        return self::create(...[...$this->elements, $element]);
    }

    public function filter_by_usage(Usage $usage): self
    {
        return self::create(...array_filter($this->elements, fn($item) => $item->usage === $usage));
    }

    public function filter_by_energie(Energie $energie): self
    {
        return self::create(...array_filter($this->elements, fn($item) => $item->energie === $energie));
    }

    public function cef(?Usage $usage = null, ?Energie $energie = null): float
    {
        $collection = $usage ? $this->filter_by_usage($usage) : $this;
        $collection = $energie ? $collection->filter_by_energie($energie) : $collection;
        return array_sum(array_map(fn($item) => $item->cef, $collection->elements));
    }

    public function cep(?Usage $usage = null, ?Energie $energie = null): float
    {
        $collection = $usage ? $this->filter_by_usage($usage) : $this;
        $collection = $energie ? $collection->filter_by_energie($energie) : $collection;
        return array_sum(array_map(fn($item) => $item->cep, $collection->elements));
    }

    public function eges(?Usage $usage = null, ?Energie $energie = null): float
    {
        $collection = $usage ? $this->filter_by_usage($usage) : $this;
        $collection = $energie ? $collection->filter_by_energie($energie) : $collection;
        return array_sum(array_map(fn($item) => $item->eges, $collection->elements));
    }

    /**
     * @return Consommation[]
     */
    public function values(): array
    {
        return $this->elements;
    }
}
