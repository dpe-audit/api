<?php

namespace App\Domain\Enveloppe\Baie;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Baie\Menuiserie\Menuiserie;
use App\Domain\Enveloppe\Baie\Position\Position;
use App\Domain\Enveloppe\Baie\Vitrage\Vitrage;
use App\Domain\Enveloppe\Baie\Survitrage\Survitrage;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Enveloppe\Masque\{Masque, MasqueCollection};
use App\Domain\Enveloppe\Paroi\{Mitoyennete, Paroi, TypeParoi};
use Webmozart\Assert\Assert;

final class Baie extends Paroi
{
    private MasqueCollection $masques;
    private BaieData $data;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeBaie $type,
        private bool $presence_protection_solaire,
        private TypeFermeture $type_fermeture,
        private ?int $annee_installation,
        private ?float $ug,
        private ?float $uw,
        private ?float $ujn,
        private ?float $sw,
        private Position $position,
        private Vitrage $vitrage,
        private ?Menuiserie $menuiserie,
        private ?Survitrage $survitrage,
    ) {
        $this->masques = new MasqueCollection();
        $this->data = BaieData::create();
    }

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        TypeBaie $type,
        bool $presence_protection_solaire,
        TypeFermeture $type_fermeture,
        ?int $annee_installation,
        ?float $ug,
        ?float $uw,
        ?float $ujn,
        ?float $sw,
        Position $position,
        Vitrage $vitrage,
        ?Menuiserie $menuiserie,
        ?Survitrage $survitrage,
    ): self {
        Assert::nullOrSame($position->double_fenetre?->enveloppe(), $enveloppe);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            type: $type,
            presence_protection_solaire: $presence_protection_solaire,
            type_fermeture: $type_fermeture,
            annee_installation: $annee_installation,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
            position: $position,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            survitrage: $survitrage,
        );
    }

    public function reinitialise(): self
    {
        $this->data = BaieData::create();
        return $this;
    }

    public function calcule(BaieData $data): self
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
        return TypeParoi::BAIE;
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeBaie
    {
        return $this->type;
    }

    public function presence_protection_solaire(): bool
    {
        return $this->presence_protection_solaire;
    }

    public function type_fermeture(): TypeFermeture
    {
        return $this->type_fermeture;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function ug(): ?float
    {
        return $this->ug;
    }

    public function uw(): ?float
    {
        return $this->uw;
    }

    public function ujn(): ?float
    {
        return $this->ujn;
    }

    public function sw(): ?float
    {
        return $this->sw;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function vitrage(): Vitrage
    {
        return $this->vitrage;
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->menuiserie;
    }

    public function survitrage(): ?Survitrage
    {
        return $this->survitrage;
    }

    /**
     * @return MasqueCollection|Masque[]
     */
    public function masques(): MasqueCollection
    {
        return $this->masques;
    }

    public function add_masque(Masque $entity): self
    {
        Assert::same($this->enveloppe, $entity->enveloppe());
        Assert::null($this->masques->find($entity->id()));

        $this->masques->add($entity);
        $this->reinitialise();

        return $this;
    }

    public function data(): BaieData
    {
        return $this->data;
    }
}
