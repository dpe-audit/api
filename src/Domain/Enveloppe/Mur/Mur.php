<?php

namespace App\Domain\Enveloppe\Mur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Lnc\Lnc;
use App\Domain\Enveloppe\Mur\Position\Position;
use App\Domain\Enveloppe\Paroi\{Inertie, Mitoyennete, Paroi, TypeParoi};
use App\Domain\Enveloppe\Paroi\Isolation\Isolation;
use Webmozart\Assert\Assert;

final class Mur extends Paroi
{
    private MurData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private ?TypeMur $type_structure,
        private ?float $epaisseur_structure,
        private TypeDoublage $type_doublage,
        private bool $presence_enduit_isolant,
        private bool $paroi_ancienne,
        private ?Inertie $inertie,
        private ?int $annee_construction,
        private ?int $annee_renovation,
        private ?float $u0,
        private ?float $u,
        private Position $position,
        private Isolation $isolation,
    ) {
        $this->data = MurData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        ?TypeMur $type_structure,
        ?float $epaisseur_structure,
        TypeDoublage $type_doublage,
        bool $presence_enduit_isolant,
        bool $paroi_ancienne,
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
            epaisseur_structure: $epaisseur_structure,
            type_doublage: $type_doublage,
            presence_enduit_isolant: $presence_enduit_isolant,
            paroi_ancienne: $paroi_ancienne,
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
        $this->data = MurData::create();
        return $this;
    }

    public function calcule(MurData $data): self
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

    /**
     * @inheritDoc
     */
    public static function type_paroi(): TypeParoi
    {
        return TypeParoi::MUR;
    }

    /**
     * @inheritDoc
     */
    public function local_non_chauffe(): ?Lnc
    {
        return $this->position->local_non_chauffe;
    }

    /**
     * @inheritDoc
     */
    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    /**
     * @inheritDoc
     */
    public function surface(): float
    {
        return $this->position->surface;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_structure(): ?TypeMur
    {
        return $this->type_structure;
    }

    public function epaisseur_structure(): ?float
    {
        return $this->epaisseur_structure;
    }

    public function type_doublage(): ?TypeDoublage
    {
        return $this->type_doublage;
    }

    public function presence_enduit_isolant(): bool
    {
        return $this->presence_enduit_isolant;
    }

    public function paroi_ancienne(): bool
    {
        return $this->paroi_ancienne;
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

    public function data(): MurData
    {
        return $this->data;
    }
}
