<?php

namespace App\Engine;

final class Context
{
    public function __construct(private Rules $rules, private Input $input, private Store $store,) {}

    public static function create(Rules $rules, Input $input): self
    {
        return new self(rules: $rules, input: $input, store: new Store());
    }

    public function rules(): Rules
    {
        return $this->rules;
    }

    public function input(): Input
    {
        return $this->input;
    }

    public function store(): Store
    {
        return $this->store;
    }
}
