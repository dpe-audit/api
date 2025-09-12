<?php

namespace App\Domain\Enveloppe\Lnc\Paroi;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Lnc\Lnc;

final class Paroi
{
    private ParoiData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Lnc $local_non_chauffe,
        private string $description,
        private ?Isolation $isolation,
        private Position $position,
    ) {
        $this->data = ParoiData::create();
    }

    public static function create(
        Id $id,
        Lnc $local_non_chauffe,
        string $description,
        ?Isolation $isolation,
        Position $position,
    ): self {
        return new self(
            id: $id,
            local_non_chauffe: $local_non_chauffe,
            description: $description,
            isolation: $isolation,
            position: $position,
        );
    }

    public function reinitialise(): self
    {
        $this->data = ParoiData::create();
        return $this;
    }

    public function calcule(ParoiData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function local_non_chauffe(): Lnc
    {
        return $this->local_non_chauffe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isolation(): ?Isolation
    {
        return $this->isolation;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function data(): ParoiData
    {
        return $this->data;
    }
}
