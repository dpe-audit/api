<?php

namespace App\Api\Chauffage\Handler;

use App\Api\Chauffage\Model\Systeme as Payload;
use App\Model\Common\ValueObject\Id;
use App\Model\Chauffage\Chauffage;
use App\Model\Chauffage\Entity\Systeme;
use App\Model\Chauffage\ValueObject\Reseau;

final class CreateSystemeHandler
{
    public function __invoke(Payload $payload, Chauffage $entity): Systeme
    {
        $id = Id::from($payload->installation_id);
        $installation = $entity->installations()->find($id);
        $generateur = $entity->generateurs()->find(Id::from($payload->generateur_id));

        $systeme = Systeme::create(
            id: $id,
            chauffage: $entity,
            installation: $installation,
            generateur: $generateur,
            reseau: $payload->reseau ? Reseau::create(
                type_distribution: $payload->reseau->type_distribution,
                presence_circulateur_externe: $payload->reseau->presence_circulateur_externe,
                niveaux_desservis: $payload->reseau->niveaux_desservis,
                isolation: $payload->reseau->isolation,
            ) : null,
        );

        foreach ($payload->emetteurs as $id) {
            $emetteur = $entity->emetteurs()->find(Id::from($id));
            $systeme->reference_emetteur($emetteur);
        }
        return $systeme;
    }
}
