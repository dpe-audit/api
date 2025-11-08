<?php

namespace App\Utils;

use Webmozart\Assert\Assert;

/**
 * @property array<array{x: float, y: float, q: ?float}> $points
 */
final class Interpolation
{
    public final const METHOD_LINEAIRE = 'lineaire';
    public final const METHOD_BILENAIRE = 'bilenaire';

    public function __construct(private array $points, private string $method)
    {
        Assert::inArray($method, [self::METHOD_LINEAIRE, self::METHOD_BILENAIRE]);

        foreach ($points as $row) {
            Assert::isArray($row);
            Assert::keyExists($row, 'x');
            Assert::keyExists($row, 'y');

            if ($this->method === self::METHOD_BILENAIRE) {
                Assert::keyExists($row, 'q');
            }
        }
    }

    public function interpolationLineaire(float $x): ?float
    {
        $this->sort('x', $x);

        foreach ($this->points as $value) {
            if ($value['x'] === $x) {
                return $value['y'];
            }
        }
        if (count($this->points) < 2) {
            return null;
        }

        $x1 = $this->points[0]['x'];
        $y1 = $this->points[0]['y'];
        $x2 = $this->points[1]['x'];
        $y2 = $this->points[1]['y'];

        return $y1 + ($x - $x1) * (($y2 - $y1) / ($x2 - $x1));
    }

    public function interpolationBilenaire(float $x, float $y): ?float
    {
        Assert::eq($this->method, self::METHOD_BILENAIRE);

        foreach ($this->points as $value) {
            if ($value['x'] === $x && $value['y'] === $y) {
                return $value['q'];
            }
        }
        $this->sort('y', $y);
        $this->sort('x', $x);

        if (count($this->points) < 4) {
            return null;
        }

        $xs = $this->xs();
        $ys = $this->ys();

        $x1 = current($xs);
        $x2 = next($xs);
        $y1 = current($ys);
        $y2 = next($ys);
        $q11 = $this->q($x1, $y1);
        $q12 = $this->q($x1, $y2);
        $q21 = $this->q($x2, $y1);
        $q22 = $this->q($x2, $y2);

        $value = (($x2 - $x) * ($y2 - $y)) / (($x2 - $x1) * ($y2 - $y1)) * $q11;
        $value += (($x - $x1) * ($y2 - $y)) / (($x2 - $x1) * ($y2 - $y1)) * $q21;
        $value += (($x2 - $x) * ($y - $y1)) / (($x2 - $x1) * ($y2 - $y1)) * $q12;
        $value += (($x - $x1) * ($y - $y1)) / (($x2 - $x1) * ($y2 - $y1)) * $q22;

        return $value;
    }

    public function xs(): array
    {
        $xs = array_map(fn(array $point) => $point['x'], $this->points);
        return array_values(array_unique($xs));
    }

    public function ys(): array
    {
        $ys = array_map(fn(array $point) => $point['y'], $this->points);
        return array_values(array_unique($ys));
    }

    public function y(float $x): ?float
    {
        return array_find($this->points, fn(array $point) => $point['x'] === $x)['y'] ?? null;
    }

    public function q(float $x, float $y): ?float
    {
        return array_find($this->points, fn(array $point) => $point['x'] === $x && $point['y'] === $y)['q'] ?? null;
    }

    private function sort(string $key, float $value): void
    {
        $points = $this->points;

        usort($points, function (array $a, array $b) use ($key, $value): float {
            $diffA = \abs($a[$key] - $value);
            $diffB = \abs($b[$key] - $value);

            if ($diffA == $diffB) {
                return 0;
            }
            return ($diffA < $diffB) ? -1 : 1;
        });

        $filter = array_map(fn(array $item) => $item[$key], $points);
        $filter = array_unique($filter);
        $filter = array_slice($filter, 0, 2);

        $this->points = array_filter($points, fn(array $item) => in_array($item[$key], $filter));
    }
}
