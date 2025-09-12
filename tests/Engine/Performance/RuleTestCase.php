<?php

namespace App\Tests\Engine\Performance;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, ZoneClimatique};
use App\Domain\Common\ValueObject\Annee;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RuleTestCase extends KernelTestCase
{
    abstract public static function filepath(): string;

    /**
     * @return array<string,string>
     */
    abstract public static function registre(): array;

    #[DataProvider('dataProvider')]
    final public function testRule(string $property, array $with, float $expected): void
    {
        self::bootKernel();
        $container = static::getContainer();

        foreach (static::registre() as $method => $class) {
            if ($method !== $property) {
                continue;
            }
            $input = static::dataNormalizer($with);
            $service = $container->get($class);

            foreach ($input as $key => $value) {
                $service->{$key} = $value;
            }
            $actual = $service->{$method}();
            $this->assertEquals($expected, $actual);
        }
    }

    final protected static function normalize(array &$input, string|array $keys, \Closure $callback): void
    {
        $keys = is_array($keys) ? $keys : [$keys];

        foreach ($keys as $key) {
            if (isset($input[$key])) {
                $input[$key] = $callback($input[$key]);
            }
        }
    }

    protected static function dataNormalizer(array $data): array
    {
        self::normalize($data, ['mois'], fn($value) => $value ? Mois::from($value) : null);
        self::normalize($data, ['scenario'], fn($value) => $value ? ScenarioUsage::from($value) : null);
        self::normalize($data, ['zone_climatique'], fn($value) => $value ? ZoneClimatique::from($value) : null);
        self::normalize($data, ['type_batiment'], fn($value) => $value ? TypeBatiment::from($value) : null);
        self::normalize($data, ['annee_construction', 'annee_renovation', 'annee_installation', 'annee_installation_generateur'], fn($value) => $value ? Annee::from($value) : null);
        return $data;
    }

    final public static function dataProvider(): array
    {
        $data = [];

        foreach (Yaml::parse(file_get_contents(static::filepath())) as $service => $tests) {
            foreach ($tests as $test) {
                $data[] = [$service, $test['with'], $test['expect']];
            }
        }
        return $data;
    }
}
