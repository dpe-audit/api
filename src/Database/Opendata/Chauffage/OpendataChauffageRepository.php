<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLElement;
use App\Model\Common\ValueObject\Id;
use App\Model\Chauffage\{Chauffage, ChauffageRepository};
use App\Serializer\Opendata\XMLChauffageDeserializer;
use App\Services\Observatoire\Observatoire;

final class OpendataChauffageRepository implements ChauffageRepository
{
    public function __construct(
        private readonly Observatoire $observatoire,
        private readonly XMLChauffageDeserializer $deserializer
    ) {}

    public function find(Id $id): ?Chauffage
    {
        if ($content = $this->observatoire->find($id)) {
            $xml = \simplexml_load_string($content, XMLElement::class);
            return $this->deserializer->deserialize($xml);
        }
        return null;
    }
}
