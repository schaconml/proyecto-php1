<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Generador del factorial de un numero.
 *
 * El factorial de n es el producto de todos los enteros desde 1 hasta n
 * (0! = 1). No esta definido para numeros negativos.
 *
 * Hasta 20! el resultado cabe en un entero de 64 bits; a partir de 21! el
 * valor excede ese rango, por lo que se calcula con la extension bcmath para
 * conservar el resultado exacto.
 */
final class FactorialGenerator implements SequenceGenerator
{
    /**
     * Cantidad minima admitida (0! = 1).
     */
    public const MIN_INPUT = 0;

    /**
     * Cantidad maxima admitida.
     */
    public const MAX_INPUT = 100;

    /**
     * Ultimo factorial que cabe en un entero de 64 bits (20! = 2432902008176640000).
     */
    private const LAST_EXACT_INT = 20;

    /**
     * Calcula el factorial del numero indicado.
     *
     * @return list<int|string> Lista con un unico elemento: el factorial.
     */
    public function generate(int $n): array
    {
        if ($n < self::MIN_INPUT) {
            throw new InvalidArgumentException('El factorial no esta definido para numeros negativos.');
        }

        if ($n > self::MAX_INPUT) {
            throw new InvalidArgumentException(
                sprintf('El factorial solo se calcula hasta %d.', self::MAX_INPUT)
            );
        }

        if ($n <= self::LAST_EXACT_INT) {
            $result = 1;

            for ($factor = 2; $factor <= $n; $factor++) {
                $result *= $factor;
            }

            return [$result];
        }

        $result = '1';

        for ($factor = 2; $factor <= $n; $factor++) {
            $result = bcmul($result, (string) $factor);
        }

        return [$result];
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
