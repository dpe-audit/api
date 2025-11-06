<?php

namespace App\Engine\Rules\Enveloppe\Apport;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\{Masque, ConfigurationMasque, SecteurMasque, TypeMasque};
use App\Engine\{Context, RuleIterator};
use App\Engine\Table\MasqueTableValeurRepository;

/**
 * @extends RuleIterator<Masque>
 */
final class FacteurEnsoleillementMasqueRule extends RuleIterator
{
    public function __construct(
        private MasqueTableValeurRepository $repository
    ) {}

    /**
     * @inheritDoc
     */
    public function collection(): array
    {
        return $this->input()->enveloppe->masques()->values();
    }

    /**
     * @inheritDoc
     */
    public function namespace(): string
    {
        return static::class . '\\' . (string) $this->item()->id();
    }

    // * Données d'entrée

    public function type(): TypeMasque
    {
        return $this->item()->type();
    }

    public function configuration(): ConfigurationMasque
    {
        return $this->item()->configuration();
    }

    public function orientation(): ?Orientation
    {
        return $this->item()->orientation();
    }

    public function secteur(): ?SecteurMasque
    {
        return $this->item()->secteur();
    }

    public function hauteur(): ?float
    {
        return $this->item()->hauteur();
    }

    public function profondeur(): ?float
    {
        return $this->item()->profondeur();
    }

    // * Données calculées

    /**
     * Facteur d'ensoleillement du masque proche
     */
    public function fe1(): float
    {
        return $this->get('fe1', function (): float {
            return $this->repository->fe1(
                configuration_masque: $this->configuration(),
                orientation_facade: $this->orientation(),
                avancee_masque: $this->profondeur(),
            ) ?? throw new \DomainException('Valeur forfaitaire FE1 non trouvée');
        });
    }

    /**
     * Facteur d'ensoleillement du masque lointain homogène
     */
    public function fe2(): float
    {
        return $this->get('fe2', function (): float {
            return $this->repository->fe2(
                configuration_masque: $this->configuration(),
                orientation_facade: $this->orientation(),
                hauteur_masque_alpha: $this->hauteur(),
            ) ?? throw new \DomainException('Valeur forfaitaire FE2 non trouvée');
        });
    }

    /**
     * Ombrage du masque lointain non homogène
     */
    public function omb(): float
    {
        return $this->get('omb', function (): float {
            return $this->repository->omb(
                configuration_masque: $this->configuration(),
                orientation_facade: $this->orientation(),
                secteur: $this->secteur(),
                hauteur_masque_alpha: $this->hauteur(),
            ) ?? throw new \DomainException('Valeur forfaitaire OMB non trouvée');
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
                fe1: $rule->fe1(),
                fe2: $rule->fe2(),
                omb: $rule->omb(),
            ));
        }
    }
}
