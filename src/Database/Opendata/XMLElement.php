<?php

namespace App\Database\Opendata;

use App\Model\Common\ValueObject\Id;

class XMLElement extends \SimpleXMLElement
{
    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        $value = \trim($this->strval());
        $value = \strtolower($value);
        $value = \str_replace('generateur:', '', $value);
        $value = \str_replace('emetteur:', '', $value);
        $value = \str_replace('ets:', '', $value);
        $value = \preg_replace('/\s/', '', $value);
        return Id::from($value);
    }

    public function getValue(): string
    {
        return (string) $this;
    }

    public function strval(): string
    {
        return \trim($this->getValue());
    }

    public function floatval(): float
    {
        return (float) $this->getValue();
    }

    public function intval(): int
    {
        return (int) $this->getValue();
    }

    public function boolval(): bool
    {
        return (bool) $this->intval();
    }
}
