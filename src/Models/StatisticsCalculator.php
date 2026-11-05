<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Calcula las medidas estadisticas de una serie de numeros reales.
 *
 * Responsabilidad unica: obtener el promedio (media aritmetica), la media
 * (mediana) y la moda de una serie de valores.
 *
 * Segun la consigna del taller:
 *  - promedio: media aritmetica (suma de los valores entre cantidad de valores).
 *  - media:    mediana (valor central; con una cantidad par, el promedio de
 *              los dos valores centrales).
 *  - moda:     valor o valores que mas veces se repiten; si todos los valores
 *              son distintos, no hay moda.
 */
final class StatisticsCalculator
{
    /**
     * Media aritmetica de la serie.
     *
     * @param list<float> $numbers
     */
    public function average(array $numbers): float
    {
        if ($numbers === []) {
            throw new \InvalidArgumentException('La serie debe contener al menos un numero.');
        }

        return array_sum($numbers) / count($numbers);
    }

    /**
     * Mediana de la serie.
     *
     * @param list<float> $numbers
     */
    public function median(array $numbers): float
    {
        if ($numbers === []) {
            throw new \InvalidArgumentException('La serie debe contener al menos un numero.');
        }

        $sorted = $numbers;
        sort($sorted);

        $total = count($sorted);
        $middle = intdiv($total, 2);

        if ($total % 2 === 1) {
            return $sorted[$middle];
        }

        return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
    }

    /**
     * Moda de la serie: los valores que mas veces se repiten.
     *
     * @param list<float> $numbers
     *
     * @return list<float> Lista vacia cuando la serie no tiene moda.
     */
    public function mode(array $numbers): array
    {
        if ($numbers === []) {
            return [];
        }

        $frequencies = [];
        $canonical = [];

        foreach ($numbers as $number) {
            // La clave canonica agrupa 1 y 1.0 como el mismo valor.
            $key = (string) $number;
            $frequencies[$key] = ($frequencies[$key] ?? 0) + 1;
            $canonical[$key] = $number;
        }

        $highest = max($frequencies);

        if ($highest < 2) {
            return [];
        }

        $modes = [];

        foreach ($frequencies as $key => $frequency) {
            if ($frequency === $highest) {
                $modes[] = $canonical[$key];
            }
        }

        sort($modes);

        return $modes;
    }
}
