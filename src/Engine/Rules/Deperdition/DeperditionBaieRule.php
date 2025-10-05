<?php

namespace App\Engine\Rules\Deperdition;

use App\Domain\Enveloppe\Paroi\{Mitoyennete, Performance};
use App\Domain\Enveloppe\Baie\TypeFermeture;
use App\Engine\Input\Enveloppe\BaieInputRuleIterator;
use App\Engine\Table\BaieTableValeurRepository;

final class DeperditionBaieRule extends BaieInputRuleIterator
{
    public function __construct(
        private BaieTableValeurRepository $repository,
    ) {}

    /**
     * Déperditions thermiques exprimées en W/K
     */
    public function dp(): float
    {
        return $this->get('dp', function (): float {
            return $this->sdep() * $this->ujn() * $this->b();
        });
    }

    /**
     * Surface déperditive en m²
     */
    public function sdep(): float
    {
        return $this->get('sdep', function (): float {
            return $this->item()->mitoyennete() !== Mitoyennete::LOCAL_RESIDENTIEL
                ? $this->item()->surface()
                : 0;
        });
    }

    /**
     * Coefficient de réduction des déperditions thermiques
     */
    public function b(): float
    {
        return $this->get('b', function (): float {
            if ($this->item()->mitoyennete() === Mitoyennete::LOCAL_NON_CHAUFFE) {
                return $this->item()->local_non_chauffe()?->b()
                    ?? $this->item()->local_non_chauffe()?->bver($this->item()->isolation())
                    ?? throw new \DomainException('Valeur b non calculée');
            }
            return $this->repository->b($this->item()->mitoyennete())
                ?? throw new \DomainException('Valeur forfaitaire b non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique du vitrage exprimé en W/m².K
     */
    public function ug(): float
    {
        return $this->get('ug', function (): float {
            return $this->ug2() ? min($this->ug1(), $this->ug2()) : $this->ug1();
        });
    }

    /**
     * Coefficient de transmission thermique du vitrage exprimé en W/m².K
     */
    public function ug1(): float
    {
        return $this->get('ug1', function (): float {
            return $this->item()->ug_saisi() ?? $this->repository->ug(
                type_baie: $this->item()->type_baie(),
                type_vitrage: $this->item()->type_vitrage(),
                nature_gaz_lame: $this->item()->nature_lame(),
                inclinaison_vitrage: $this->item()->inclinaison(),
                epaisseur_lame_air: $this->item()->epaisseur_lame(),
            ) ?? throw new \DomainException('Valeur forfaitaire ug non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique de la double fenêtre exprimé en W/m².K
     */
    public function ug2(): ?float
    {
        return $this->get('ug2', function (): ?float {
            return $this->item()->double_fenetre()?->ug();
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie exprimé en W/m².K
     */
    public function uw(): float
    {
        return $this->get('uw', function (): float {
            return $this->uw2()
                ? 1 / (1 / $this->uw1() + 1 / $this->uw2() + 0.07)
                : $this->uw1();
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie exprimé en W/m².K
     */
    public function uw1(): float
    {
        return $this->get('uw1', function (): float {
            return $this->item()->uw_saisi() ?? $this->repository->uw(
                ug: $this->ug1(),
                type_baie: $this->item()->type_baie(),
                presence_soubassement: $this->item()->presence_soubassement(),
                materiau: $this->item()->materiau(),
                presence_rupteur_pont_thermique: $this->item()->presence_rupteur_pont_thermique(),
            ) ?? throw new \DomainException('Valeur forfaitaire uw non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique de la double fenêtre exprimé en W/m².K
     */
    public function uw2(): ?float
    {
        return $this->get('uw2', function (): ?float {
            return $this->item()->double_fenetre()?->uw();
        });
    }

    /**
     * Résistance thermique additionnelle due aux fermetures exprimée en m².K/W
     */
    public function deltar(): float
    {
        return $this->get('deltar', function (): float {
            if ($this->item()->type_fermeture() === TypeFermeture::SANS_FERMETURE) {
                return 0;
            }
            return $this->repository->deltar($this->item()->type_fermeture())
                ?? throw new \DomainException('Valeur forfaitaire deltar non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie avec fermetures exprimé en W/m².K
     */
    public function ujn(): float
    {
        return $this->get('ujn', function (): float {
            if ($this->item()->ujn_saisi()) {
                return $this->item()->ujn_saisi();
            }
            if ($this->item()->type_fermeture() === TypeFermeture::SANS_FERMETURE) {
                return $this->uw();
            }
            return $this->repository->ujn(
                deltar: $this->deltar(),
                uw: $this->uw(),
            ) ?? throw new \DomainException('Valeur forfaitaire ujn non trouvée');
        });
    }

    /**
     * Etat de performance de la baie
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_ubaie($this->ujn());
        });
    }

    /**
     * @inheritDoc
     */
    public function calcule(): void
    {
        $this->item()->entity->calcule($this->item()->entity->data()->with(
            sdep: $this->sdep(),
            b: $this->b(),
            ug: $this->ug(),
            uw: $this->uw(),
            deltar: $this->deltar(),
            u: $this->ujn(),
            performance: $this->performance(),
            dp: $this->dp(),
        ));
    }
}
