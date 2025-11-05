<?php

namespace App\Domain\Common\Perte;

/**
 * @property Perte[] $elements
 */
final class PerteCollection
{
    public function __construct(private array $elements = []) {}

    public static function create(Perte ...$elements): self
    {
        /** @var Perte[] $unique */
        $unique = [];

        foreach ($elements as $element) {
            $key = $element->type->value;

            if (isset($unique[$key])) {
                $unique[$key] = Perte::create(
                    type: $element->type,
                    pertes: $unique[$key]->pertes + $element->pertes,
                    pertes_recuperables: $unique[$key]->pertes_recuperables + $element->pertes_recuperables,
                );
            } else {
                $unique[$key] = $element;
            }
        }
        return new self(array_values($unique));
    }

    public function with(Perte $element): self
    {
        return self::create(...[...$this->elements, $element]);
    }

    public function filter_by_type(TypePerte $type): self
    {
        return self::create(...array_filter($this->elements, fn($item) => $item->type === $type));
    }

    public function pertes(?TypePerte $type = null): float
    {
        $collection = $type ? $this->filter_by_type($type) : $this;
        return array_sum(array_map(fn($item) => $item->pertes, $collection->elements));
    }

    public function pertes_recuperables(?TypePerte $type = null): float
    {
        $collection = $type ? $this->filter_by_type($type) : $this;
        return array_sum(array_map(fn($item) => $item->pertes_recuperables, $collection->elements));
    }

    /**
     * @return Perte[]
     */
    public function values(): array
    {
        return $this->elements;
    }
}
