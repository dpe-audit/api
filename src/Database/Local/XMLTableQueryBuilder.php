<?php

namespace App\Database\Local;

final class XMLTableQueryBuilder
{
    private string $query = '//';

    public function __construct(
        private readonly XMLTableDatabase $db,
    ) {}

    public function and(string $attribute, mixed $value): static
    {
        if ($this->query === '//') {
            $this->query .= 'row';
        }
        $search = $value;

        if (\is_bool($search)) {
            $search = (int) $search;
        }
        if (null === $search) {
            $search = '';
        }
        if ($search instanceof \Stringable) {
            $search = (string) $search;
        }
        if ($search instanceof \BackedEnum) {
            $search = (string) $search->value;
        }
        $expression = '$attribute = "$search" or $attribute = ""';
        if (null === $value) {
            $expression .= ' or $attribute = "inconnu"';
        }

        $expression = \str_replace(['$attribute', '$search'], [$attribute, $search], $expression);
        $this->query .= "[{$expression}]";
        return $this;
    }

    public function andCompareTo(string $attribute, null|int|float $value): static
    {
        if ($this->query === '//') {
            $this->query .= 'row';
        }
        if (null === $value) {
            $value = '""';
        }

        $queries = [];
        $queries[] = '$attribute/@lt = "" or $attribute/@lt > $value';
        $queries[] = '$attribute/@lte = "" or $attribute/@lte >= $value';
        $queries[] = '$attribute/@gt = "" or $attribute/@gt < $value';
        $queries[] = '$attribute/@gte = "" or $attribute/@gte <= $value';
        $queries[] = '$attribute/@eq = "" or $attribute/@eq = $value';

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
