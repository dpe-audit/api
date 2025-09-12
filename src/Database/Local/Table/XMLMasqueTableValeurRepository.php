<?php

namespace App\Database\Local\Table;

use App\Database\Local\XMLTableDatabase;
use App\Domain\Common\Enum\Orientation;
use App\Domain\Enveloppe\Masque\ConfigurationMasque;
use App\Domain\Enveloppe\Masque\SecteurMasque;
use App\Engine\Table\MasqueTableValeurRepository;

final class XMLMasqueTableValeurRepository implements MasqueTableValeurRepository
{
    public function __construct(protected readonly XMLTableDatabase $db) {}

    public function fe1(
        ConfigurationMasque $configuration_masque,
        Orientation $orientation_facade,
        ?float $avancee_masque,
    ): ?float {
        return $this->db->repository('masque.fe1')
            ->createQuery()
            ->and('configuration_masque', $configuration_masque)
            ->and('orientation_facade', $orientation_facade)
            ->andCompareTo('avancee_masque', $avancee_masque)
            ->getOne()
            ?->floatval('fe1');
    }

    public function fe2(
        ConfigurationMasque $configuration_masque,
        Orientation $orientation_facade,
        float $hauteur_masque_alpha,
    ): ?float {
        return $this->db->repository('masque.fe2')
            ->createQuery()
            ->and('configuration_masque', $configuration_masque)
            ->and('orientation_facade', $orientation_facade)
            ->andCompareTo('hauteur_masque_alpha', $hauteur_masque_alpha)
            ->getOne()
            ?->floatval('fe2');
    }

    public function omb(
        ConfigurationMasque $configuration_masque,
        SecteurMasque $secteur,
        Orientation $orientation_facade,
        float $hauteur_masque_alpha,
    ): ?float {
        return $this->db->repository('masque.omb')
            ->createQuery()
            ->and('configuration_masque', $configuration_masque)
            ->and('secteur', $secteur)
            ->and('orientation_facade', $orientation_facade)
            ->andCompareTo('hauteur_masque_alpha', $hauteur_masque_alpha)
            ->getOne()
            ?->floatval('omb');
    }
}
