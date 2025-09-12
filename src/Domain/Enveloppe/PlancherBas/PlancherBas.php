<?php

namespace App\Domain\Enveloppe\PlancherBas;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Paroi\{Paroi, TypeParoi};
use App\Domain\Enveloppe\PlancherBas\Isolation\Isolation;
use App\Domain\Enveloppe\PlancherBas\Position\Position;
use Webmozart\Assert\Assert;

final class PlancherBas extends Paroi
{
    private PlancherBasData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private ?TypePlancherBas $type_structure,
        private ?Inertie $inertie,
        private ?int $annee_construction,
        private ?int $annee_renovation,
        private ?float $u0,
        private ?float $u,
        private Position $position,
        private Isolation $isolation,
    ) {
        $this->data = PlancherBasData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        ?TypePlancherBas $type_structure,
        ?Inertie $inertie,
        ?int $annee_construction,
        ?int $annee_renovation,
        ?float $u0,
        ?float $u,
        Position $position,
        Isolation $isolation,
    ): self {
        Assert::nullOrSame($position->local_non_chauffe?->enveloppe(), $enveloppe);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type_structure: $type_structure,
            inertie: $inertie,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
            position: $position,
            isolation: $isolation,
        );
    }

    public function reinitialise(): self
    {
        $this->data = PlancherBasData::create();
        return $this;
    }

    public function calcule(PlancherBasData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public static function type_paroi(): TypeParoi
    {
        return TypeParoi::PLANCHER_BAS;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_structure(): ?TypePlancherBas
    {
        return $this->type_structure;
    }

    public function inertie(): ?Inertie
    {
        return $this->inertie;
    }

    public function annee_construction(): ?int
    {
        return $this->annee_construction;
    }

    public function annee_renovation(): ?int
    {
        return $this->annee_renovation;
    }

    public function u0(): ?float
    {
        return $this->u0;
    }

    public function u(): ?float
    {
        return $this->u;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function isolation(): Isolation
    {
        return $this->isolation;
    }

    public function data(): PlancherBasData
    {
        return $this->data;
    }
}
