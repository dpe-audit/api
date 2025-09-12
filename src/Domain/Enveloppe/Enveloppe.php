<?php

namespace App\Domain\Enveloppe;

use App\Domain\Enveloppe\Lnc\{Lnc, LncCollection};
use App\Domain\Enveloppe\Baie\{Baie, BaieCollection};
use App\Domain\Enveloppe\DoubleFenetre\{DoubleFenetre, DoubleFenetreCollection};
use App\Domain\Enveloppe\Mur\{Mur, MurCollection};
use App\Domain\Enveloppe\Masque\{Masque, MasqueCollection};
use App\Domain\Enveloppe\Niveau\{Niveau, NiveauCollection};
use App\Domain\Enveloppe\Paroi\{Paroi, ParoiCollection};
use App\Domain\Enveloppe\PlancherBas\{PlancherBas, PlancherBasCollection};
use App\Domain\Enveloppe\PlancherHaut\{PlancherHaut, PlancherHautCollection};
use App\Domain\Enveloppe\Porte\{Porte, PorteCollection};
use App\Domain\Enveloppe\PontThermique\{PontThermique, PontThermiqueCollection};
use Webmozart\Assert\Assert;

final class Enveloppe
{
    private NiveauCollection $niveaux;
    private LncCollection $locaux_non_chauffes;
    private DoubleFenetreCollection $doubles_fenetres;
    private MasqueCollection $masques;
    private BaieCollection $baies;
    private MurCollection $murs;
    private PlancherBasCollection $planchers_bas;
    private PlancherHautCollection $planchers_hauts;
    private PorteCollection $portes;
    private PontThermiqueCollection $ponts_thermiques;
    private EnveloppeData $data;

    public function __construct(
        private Exposition $exposition,
        private ?float $q4pa_conv,
        private bool $presence_brasseurs_air,
    ) {
        $this->niveaux = new NiveauCollection();
        $this->locaux_non_chauffes = new LncCollection();
        $this->doubles_fenetres = new DoubleFenetreCollection();
        $this->masques = new MasqueCollection();
        $this->baies = new BaieCollection();
        $this->murs = new MurCollection();
        $this->planchers_bas = new PlancherBasCollection();
        $this->planchers_hauts = new PlancherHautCollection();
        $this->portes = new PorteCollection();
        $this->ponts_thermiques = new PontThermiqueCollection();
        $this->data = EnveloppeData::create();
    }

    public static function create(
        Exposition $exposition,
        ?float $q4pa_conv,
        bool $presence_brasseurs_air,
    ): self {
        return new self(
            exposition: $exposition,
            q4pa_conv: $q4pa_conv,
            presence_brasseurs_air: $presence_brasseurs_air,
        );
    }

    public function reinitialise(): void
    {
        $this->data = EnveloppeData::create();
        $this->niveaux->reinitialise();
        $this->locaux_non_chauffes->reinitialise();
        $this->doubles_fenetres->reinitialise();
        $this->masques->reinitialise();
        $this->baies->reinitialise();
        $this->murs->reinitialise();
        $this->planchers_bas->reinitialise();
        $this->planchers_hauts->reinitialise();
        $this->portes->reinitialise();
        $this->ponts_thermiques->reinitialise();
    }

    public function calcule(EnveloppeData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function exposition(): Exposition
    {
        return $this->exposition;
    }

    public function q4pa_conv(): ?float
    {
        return $this->q4pa_conv;
    }

    public function presence_brasseurs_air(): bool
    {
        return $this->presence_brasseurs_air;
    }

    /**
     * @return ParoiCollection|Paroi[]
     */
    public function parois(): ParoiCollection
    {
        return new ParoiCollection(array_merge(
            $this->murs->values(),
            $this->planchers_bas->values(),
            $this->planchers_hauts->values(),
            $this->portes->values(),
            $this->baies->values(),
        ));
    }

    /**
     * @return NiveauCollection|Niveau[]
     */
    public function niveaux(): NiveauCollection
    {
        return $this->niveaux;
    }

    public function add_niveau(Niveau $entity): self
    {
        Assert::null($this->niveaux->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->niveaux->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return LncCollection|Lnc[]
     */
    public function locaux_non_chauffes(): LncCollection
    {
        return $this->locaux_non_chauffes;
    }

    public function add_local_non_chauffe(Lnc $entity): self
    {
        Assert::null($this->locaux_non_chauffes->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->locaux_non_chauffes->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return DoubleFenetreCollection|DoubleFenetre[]
     */
    public function doubles_fenetres(): DoubleFenetreCollection
    {
        return $this->doubles_fenetres;
    }

    public function add_double_fenetre(DoubleFenetre $entity): self
    {
        Assert::null($this->doubles_fenetres->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->doubles_fenetres->add($entity);
        $this->reinitialise();

        return $this;
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
        Assert::null($this->masques->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->masques->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return BaieCollection|Baie[]
     */
    public function baies(): BaieCollection
    {
        return $this->baies;
    }

    public function add_baie(Baie $entity): self
    {
        Assert::null($this->baies->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->baies->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return MurCollection|Mur[]
     */
    public function murs(): MurCollection
    {
        return $this->murs;
    }

    public function add_mur(Mur $entity): self
    {
        Assert::null($this->murs->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->murs->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return PlancherBasCollection|PlancherBas[]
     */
    public function planchers_bas(): PlancherBasCollection
    {
        return $this->planchers_bas;
    }

    public function add_plancher_bas(PlancherBas $entity): self
    {
        Assert::null($this->planchers_bas->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->planchers_bas->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return PlancherHautCollection|PlancherHaut[]
     */
    public function planchers_hauts(): PlancherHautCollection
    {
        return $this->planchers_hauts;
    }

    public function add_plancher_haut(PlancherHaut $entity): self
    {
        Assert::null($this->planchers_hauts->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->planchers_hauts->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return PorteCollection|Porte[]
     */
    public function portes(): PorteCollection
    {
        return $this->portes;
    }

    public function add_porte(Porte $entity): self
    {
        Assert::null($this->portes->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->portes->add($entity);
        $this->reinitialise();

        return $this;
    }

    /**
     * @return PontThermiqueCollection|PontThermique[]
     */
    public function ponts_thermiques(): PontThermiqueCollection
    {
        return $this->ponts_thermiques;
    }

    public function add_pont_thermique(PontThermique $entity): self
    {
        Assert::null($this->ponts_thermiques->find($entity->id()));
        Assert::same($entity->enveloppe(), $this);

        $this->ponts_thermiques->add($entity);
        $this->reinitialise();

        return $this;
    }

    public function data(): EnveloppeData
    {
        return $this->data;
    }
}
