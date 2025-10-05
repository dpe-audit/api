<?php

namespace App\Domain\Enveloppe\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Paroi\{Mitoyennete, Paroi, TypeParoi};
use App\Domain\Enveloppe\Porte\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\Porte\Position\Position;
use App\Domain\Enveloppe\Porte\Vitrage\Vitrage;
use Webmozart\Assert\Assert;

final class Porte extends Paroi
{
    private PorteData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private ?Isolation $isolation,
        private ?Materiau $materiau,
        private ?int $annee_installation,
        private ?float $u,
        private Position $position,
        private Menuiserie $menuiserie,
        private Vitrage $vitrage,
    ) {
        $this->data = PorteData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        ?Isolation $isolation,
        ?Materiau $materiau,
        ?int $annee_installation,
        ?float $u,
        Position $position,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
    ): self {
        Assert::nullOrlessThanEq($vitrage->surface / $position->surface, 0.6);
        Assert::nullOrSame($position->local_non_chauffe?->enveloppe(), $enveloppe);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            isolation: $isolation,
            materiau: $materiau,
            annee_installation: $annee_installation,
            u: $u,
            position: $position,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
        );
    }

    public function reinitialise(): self
    {
        $this->data = PorteData::create();
        return $this;
    }

    public function calcule(PorteData $data): self
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
        return TypeParoi::PORTE;
    }

    /**
     * @inheritDoc
     */
    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function isolation(): ?Isolation
    {
        return $this->isolation;
    }

    public function materiau(): ?Materiau
    {
        return $this->materiau;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function u(): ?float
    {
        return $this->u;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function menuiserie(): Menuiserie
    {
        return $this->menuiserie;
    }

    public function vitrage(): Vitrage
    {
        return $this->vitrage;
    }

    public function data(): PorteData
    {
        return $this->data;
    }
}
