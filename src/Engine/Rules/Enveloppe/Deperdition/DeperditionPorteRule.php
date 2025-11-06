<?php

namespace App\Engine\Rules\Enveloppe\Deperdition;

use App\Domain\Enveloppe\Paroi\{Performance, TypeParoi};
use App\Domain\Enveloppe\Porte\{Isolation, Materiau, Porte};
use App\Domain\Enveloppe\Porte\Vitrage\TypeVitrage;
use App\Engine\Context;
use App\Engine\Table\PorteTableValeurRepository;

/**
 * @extends DeperditionParoiRule<Porte>
 */
final class DeperditionPorteRule extends DeperditionParoiRule
{
    public function __construct(
        private PorteTableValeurRepository $repository
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->portes()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type_paroi(): TypeParoi
    {
        return $this->item()->type_paroi();
    }

    public function presence_sas(): bool
    {
        return $this->item()->position()->presence_sas;
    }

    public function isolation(): bool
    {
        return $this->item()->isolation() === Isolation::ISOLE;
    }

    public function materiau(): Materiau
    {
        return $this->item()->materiau() ?? Materiau::PVC;
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return $this->item()->vitrage()->surface
            ? $this->item()->vitrage()->type ?? TypeVitrage::SIMPLE_VITRAGE
            : null;
    }

    public function taux_vitrage(): float
    {
        return $this->item()->vitrage()->surface
            ? $this->item()->vitrage()->surface / $this->item()->position()->surface * 100
            : 0;
    }

    public function presence_joint(): bool
    {
        return $this->item()->menuiserie()?->presence_joint ?? false;
    }

    public function u_saisi(): ?float
    {
        return $this->item()->u();
    }

    // * Données calculées

    /**
     * Coefficient de transmission thermique exprimé en W/m².K
     */
    public function u(): float
    {
        return $this->get('u', function (): float {
            return $this->u_saisi() ?? $this->repository->u(
                presence_sas: $this->presence_sas(),
                isolation: $this->isolation(),
                materiau: $this->materiau(),
                type_vitrage: $this->type_vitrage(),
                taux_vitrage: $this->taux_vitrage(),
            ) ?? throw new \DomainException('Valeur forfaitaire Uporte non trouvée');
        });
    }

    /**
     * Etat de performance de la porte
     */
    public function performance(): Performance
    {
        return $this->get('performance', function (): Performance {
            return Performance::from_uporte($this->u());
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
                u: $rule->u(),
                dp: $rule->dp(),
                performance: $rule->performance(),
            ));
        }
    }
}
