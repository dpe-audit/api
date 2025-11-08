<?php

namespace App\Database\Local;

final class XMLTableQueryBuilder
{
    private string $query = '//row';

    public function __construct(
        private readonly XMLTableDatabase $db,
    ) {}

    public function and(string $attribute, mixed $value, bool $strict = true): static
    {
        if (\is_bool($value)) {
            $value = (int) $value;
        }
        if (null === $value) {
            $value = '';
        }
        if ($value instanceof \Stringable) {
            $value = (string) $value;
        }
        if ($value instanceof \BackedEnum) {
            $value = (string) $value->value;
        }
        $expression = '$attribute = "$value" or $attribute = ""';

        if (false === $strict && \is_string($value) && $value !== '') {
            $expression .= 'or $attribute[contains(text(), "$value")]';
        }
        if (null === $value || $value === '') {
            $expression .= ' or $attribute = "inconnu"';
        }

        $expression = \str_replace(['$attribute', '$value'], [$attribute, $value], $expression);
        $this->query .= "[{$expression}]";

        return $this;
    }

    public function andCompareTo(string $attribute, null|int|float $value): static
    {
        if (null === $value) {
            $value = '""';
        }
        $queries = [];
        $queries[] = 'not($attribute/@lt) or $attribute/@lt = "" or $attribute/@lt > $value';
        $queries[] = 'not($attribute/@lte) or $attribute/@lte = "" or $attribute/@lte >= $value';
        $queries[] = 'not($attribute/@gt) or $attribute/@gt = "" or $attribute/@gt < $value';
        $queries[] = 'not($attribute/@gte) or $attribute/@gte = "" or $attribute/@gte <= $value';
        $queries[] = 'not($attribute/@eq) or $attribute/@eq = "" or $attribute/@eq = $value';

        foreach ($queries as $query) {
            $query = \str_replace(['$attribute', '$value'], [$attribute, $value], $query);
            $this->query .= "[{$query}]";
        }

        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getOne(): ?XMLTableElement
    {
        return $this->db->getOne($this);
    }

    public function getMany(): XMLTableCollection
    {
        return $this->db->getMany($this);
    }
}
