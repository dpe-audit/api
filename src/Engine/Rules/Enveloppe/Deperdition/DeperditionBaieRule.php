<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Baie\{Baie, TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Baie\Menuiserie\Materiau;
use App\Domain\Enveloppe\Baie\Survitrage\TypeSurvitrage;
use App\Domain\Enveloppe\Baie\Vitrage\{NatureGazLame, TypeVitrage};
use App\Domain\Enveloppe\Paroi\Performance;
use App\Engine\Context;
use App\Engine\Table\BaieTableValeurRepository;

/**
 * @extends DeperditionParoiRule<Baie>
 */
final class DeperditionBaieRule extends DeperditionParoiRule
{
    public function __construct(private BaieTableValeurRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->baies()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function ug_saisi(): ?float
    {
        return $this->item()->ug();
    }

    public function uw_saisi(): ?float
    {
        return $this->item()->uw();
    }

    public function ujn_saisi(): ?float
    {
        return $this->item()->ujn();
    }

    public function type_baie(): TypeBaie
    {
        return $this->item()->type();
    }

    public function type_vitrage(): TypeVitrage
    {
        if ($this->survitrage() && false === $this->item()->vitrage()->type->vitrage_complexe()) {
            return TypeVitrage::DOUBLE_VITRAGE;
        }
        return $this->item()->vitrage()->type;
    }

    public function survitrage(): bool
    {
        return null !== $this->item()->survitrage();
    }

    public function type_fermeture(): TypeFermeture
    {
        return $this->item()->type_fermeture();
    }

    public function isolation(): bool
    {
        return $this->type_vitrage()->isolation();
    }

    public function inclinaison(): float
    {
        return $this->item()->position()->inclinaison;
    }

    public function presence_soubassement(): bool
    {
        return $this->item()->position()->presence_soubassement ?? false;
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->item()->menuiserie()?->presence_rupteur_pont_thermique ?? false;
    }

    public function materiau(): Materiau
    {
        return $this->item()->menuiserie()?->materiau ?? Materiau::PVC;
    }

    public function epaisseur_lame(): float
    {
        if ($this->item()->vitrage()->epaisseur_lame) {
            return $this->item()->vitrage()->epaisseur_lame;
        }
        if ($this->item()->vitrage()->type->vitrage_complexe()) {
            return 6;
        }
        if ($this->survitrage()) {
            return $this->item()->survitrage()->epaisseur_lame ?? 6;
        }
        return 0;
    }

    public function nature_lame(): ?NatureGazLame
    {
        if ($this->item()->vitrage()->nature_lame) {
            return $this->item()->vitrage()->nature_lame;
        }
        if ($this->item()->vitrage()->type->vitrage_complexe()) {
            return NatureGazLame::AIR;
        }
        return $this->survitrage() ? NatureGazLame::AIR : null;
    }

    // * Données intermédiaires

    /**
     * Coefficient de transmission thermique de la double fenêtre exprimé en W/m².K
     */
    public function ug2(): ?float
    {
        return $this->get('ug2', function (): ?float {
            return ($entity = $this->item()->position()->double_fenetre)
                ? $this->requireIterator(DeperditionDoubleFenetreRule::class, $entity)->ug()
                : null;
        });
    }

    /**
     * Coefficient de transmission thermique de la double fenêtre exprimé en W/m².K
     */
    public function uw2(): ?float
    {
        return $this->get('uw2', function (): ?float {
            return ($entity = $this->item()->position()->double_fenetre)
                ? $this->requireIterator(DeperditionDoubleFenetreRule::class, $entity)->uw()
                : null;
        });
    }

    // * Données calculées

    /**
     * @inheritDoc
     */
    public function u(): float
    {
        return $this->ujn();
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
            $value = $this->ug_saisi() ?? $this->repository->ug(
                type_vitrage: $this->type_vitrage(),
                type_baie: $this->type_baie(),
                nature_gaz_lame: $this->nature_lame(),
                inclinaison_vitrage: $this->inclinaison(),
                epaisseur_lame_air: $this->epaisseur_lame(),
            ) ?? throw new \DomainException('Valeur forfaitaire ug non trouvée');

            return $this->survitrage() ? $value + 0.1 : $value;
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
            return $this->uw_saisi() ?? $this->repository->uw(
                ug: $this->ug1(),
                type_baie: $this->type_baie(),
                presence_soubassement: $this->presence_soubassement(),
                materiau: $this->materiau(),
                presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
            ) ?? throw new \DomainException('Valeur forfaitaire uw non trouvée');
        });
    }

    /**
     * Résistance thermique additionnelle due aux fermetures exprimée en m².K/W
     */
    public function deltar(): float
    {
        return $this->get('deltar', function (): float {
            if ($this->type_fermeture() === TypeFermeture::SANS_FERMETURE) {
                return 0;
            }
            return $this->repository->deltar($this->type_fermeture())
                ?? throw new \DomainException('Valeur forfaitaire deltar non trouvée');
        });
    }

    /**
     * Coefficient de transmission thermique de la menuiserie avec fermetures exprimé en W/m².K
     */
    public function ujn(): float
    {
        return $this->get('ujn', function (): float {
            if ($this->ujn_saisi()) {
                return $this->ujn_saisi();
            }
            if ($this->type_fermeture() === TypeFermeture::SANS_FERMETURE) {
                return $this->uw();
            }
            return $this->repository->ujn(deltar: $this->deltar(), uw: $this->uw())
                ?? throw new \DomainException('Valeur forfaitaire ujn non trouvée');
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
    public function __invoke(mixed $data, Context $context): void
    {
        parent::__invoke($data, $context);

        foreach ($this as $rule) {
            $rule->item()->calcule($rule->item()->data()->with(
                sdep: $rule->sdep(),
                b: $rule->b(),
                ug: $rule->ug(),
                uw: $rule->uw(),
                deltar: $rule->deltar(),
                u: $rule->u(),
                performance: $rule->performance(),
                dp: $rule->dp(),
            ));
        }
    }
}
