<?php

namespace App\Tests\Schema;

use App\Dto\Diagnostic\DiagnosticDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class SchemaDiagnosticTest extends KernelTestCase
{
    public function testSchema(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SerializerInterface $serializer */
        $serializer = $container->get(SerializerInterface::class);
        $data = file_get_contents( __DIR__ . '/diagnostic.yaml');

        $payload = $serializer->deserialize($data, DiagnosticDto::class, 'yaml');
    }
}