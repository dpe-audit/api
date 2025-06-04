<?php

namespace App\Tests\Engine\Performance;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RuleTestCase extends KernelTestCase
{
    abstract public static function filepath(): string;

    #[DataProvider('dataProvider')]
    final public function testRule(string $service, array $with, array $expected): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $rule = $container->get($service);
        $input = static::dataNormalizer($with);
        $output = $rule($input);

        $this->assertEqualsCanonicalizing($expected, $output);
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

    protected static function dataNormalizer(array $input): array
    {
        self::normalize($input, ['zone_climatique'], fn($value) => $value ? ZoneClimatique::from($value) : null);
        self::normalize($input, ['annee_construction', 'annee_renovation', 'annee_installation', 'annee_installation_generateur'], fn($value) => $value ? Annee::from($value) : null);
        return $input;
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
