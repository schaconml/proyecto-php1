<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Generador de la sucesion de Fibonacci.
 *
 * La sucesion comienza en 0, 1 y cada termino es la suma de los dos anteriores:
 * 0, 1, 1, 2, 3, 5, 8, 13, 21, 34, ...
 *
 * El algoritmo es iterativo (una sola pasada), de modo que no se repite el
 * trabajo de la recursion y el costo es lineal en el numero de terminos.
 */
final class FibonacciGenerator implements SequenceGenerator
{
    /**
     * Cantidad minima de terminos admitidos.
     */
    public const MIN_INPUT = 1;

    /**
     * Cantidad maxima de terminos admitidos.
     */
    public const MAX_INPUT = 100;

    /**
     * Genera los primeros terminos de la sucesion de Fibonacci.
     *
     * @return list<int>
     */
    public function generate(int $n): array
    {
        if ($n < self::MIN_INPUT) {
            return [];
        }

        $terms = [0];

        if ($n === 1) {
            return $terms;
        }

        $terms[] = 1;

        for ($position = 2; $position < $n; $position++) {
            $terms[] = $terms[$position - 1] + $terms[$position - 2];
        }

        return $terms;
    }

    public function minInput(): int
    {
        return self::MIN_INPUT;
    }

    public function maxInput(): int
    {
        return self::MAX_INPUT;
    }
}
