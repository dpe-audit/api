<?php

namespace App\Domain\Production;

use App\Domain\Production\PanneauPhotovoltaique\{PanneauPhotovoltaique, PanneauPhotovoltaiqueCollection};
use Webmozart\Assert\Assert;

final class Production
{
    private PanneauPhotovoltaiqueCollection $panneaux_photovoltaiques;
    private ProductionData $data;

    public function __construct()
    {
        $this->panneaux_photovoltaiques = new PanneauPhotovoltaiqueCollection;
        $this->data = ProductionData::create();
    }

    public static function create(): self
    {
        return new self();
    }

    public function calcule(ProductionData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = ProductionData::create();
        $this->panneaux_photovoltaiques->reinitialise();
    }

    /**
     * @return PanneauPhotovoltaiqueCollection|PanneauPhotovoltaique[]
     */
    public function panneaux_photovoltaiques(): PanneauPhotovoltaiqueCollection
    {
        return $this->panneaux_photovoltaiques;
    }

    public function add_panneau_photovoltaique(PanneauPhotovoltaique $entity): self
    {
        Assert::null($this->panneaux_photovoltaiques->find($entity->id()));
        Assert::same($entity->production(), $this);

        $this->panneaux_photovoltaiques->add($entity);
        $this->reinitialise();

        return $this;
    }

    public function data(): ProductionData
    {
        return $this->data;
    }
}
