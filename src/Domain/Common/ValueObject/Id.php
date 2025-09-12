<?php

namespace App\Domain\Common\ValueObject;

use Symfony\Component\Uid\{AbstractUid, Uuid};

final class Id extends AbstractUid
{
    public function __construct(protected string $uid) {}

    public static function create(): static
    {
        return new static(uid: Uuid::v7()->toRfc4122());
    }

    public function toBinary(): string
    {
        return $this->uid;
    }

    public static function isValid(string $uid): bool
    {
        return Uuid::isValid($uid);
    }

    public static function fromString(string $uid): static
    {
        if (!static::isValid($uid)) {
            throw new \InvalidArgumentException('Invalid uid provided.');
        }
        return new static(uid: $uid);
    }
}
