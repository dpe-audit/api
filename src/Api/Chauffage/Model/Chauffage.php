<?php

namespace App\Api\Chauffage\Model;

use App\Model\Chauffage\Chauffage as Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property Generateur[] $generateurs
 * @property Emetteur[] $emetteurs
 * @property Installation[] $installations
 * @property Systeme[] $systemes
 */
final class Chauffage
{
    public function __construct(
        public string $id,

        /** @var Generateur[] */
        #[Assert\All([new Assert\Type(Generateur::class)])]
        #[Assert\Valid]
        public array $generateurs,

        /** @var Emetteur[] */
        #[Assert\All([new Assert\Type(Emetteur::class)])]
        #[Assert\Valid]
        public array $emetteurs,

        /** @var Installation[] */
        #[Assert\All([new Assert\Type(Installation::class)])]
        #[Assert\Valid]
        public array $installations,

        /** @var Systeme[] */
        #[Assert\All([new Assert\Type(Systeme::class)])]
        #[Assert\Valid]
        public array $systemes,

        public ?ChauffageData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            emetteurs: Emetteur::from_collection($entity->emetteurs()),
            installations: Installation::from_collection($entity->installations()),
            systemes: Systeme::from_collection($entity->systemes()),
            data: ChauffageData::from($entity),
        );
    }

    #[Assert\IsTrue]
    public function is_generateur_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($this->generateurs as $generateur) {
                if ($generateur->id === $systeme->generateur_id) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    #[Assert\IsTrue]
    public function is_installation_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($this->installations as $installation) {
                if ($installation->id === $systeme->installation_id) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    #[Assert\IsTrue]
    public function is_emetteur_exists(): bool
    {
        foreach ($this->systemes as $systeme) {
            foreach ($systeme->emetteurs as $emetteur_id) {
                foreach ($this->emetteurs as $emetteur) {
                    if ($emetteur->id === $emetteur_id) {
                        return true;
                    }
                }
                return false;
            }
        }
        return true;
    }
}
