<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Base comun de los reconstructores de arboles binarios.
 *
 * Concentra las comprobaciones que todos los pares de recorridos comparten
 * (misma longitud, sin valores repetidos y mismo conjunto de valores) y la
 * busqueda de un valor dentro de un rango de un recorrido.
 *
 * Cada subclase implementa unicamente el algoritmo que le corresponde.
 */
abstract class AbstractTreeBuilder implements TreeBuilder
{
    /**
     * @param list<string> $first
     * @param list<string> $second
     */
    protected function assertCompatible(array $first, array $second): void
    {
        if (count($first) !== count($second)) {
            throw new InvalidArgumentException(sprintf(
                'Los recorridos deben tener la misma cantidad de nodos (%d y %d).',
                count($first),
                count($second)
            ));
        }

        $this->assertNoDuplicates($first);
        $this->assertNoDuplicates($second);

        $missing = array_diff($first, $second);
        $extra = array_diff($second, $first);

        if ($missing !== [] || $extra !== []) {
            throw new InvalidArgumentException(
                'Los recorridos no contienen los mismos nodos, por lo que no describen el mismo arbol.'
            );
        }
    }

    /**
     * @param list<string> $values
     */
    protected function assertNoDuplicates(array $values): void
    {
        if (count($values) !== count(array_unique($values))) {
            $repeated = array_values(array_diff_assoc($values, array_unique($values)));
            $repeated = array_values(array_unique($repeated));

            throw new InvalidArgumentException(sprintf(
                'Un recorrido no puede repetir nodos. El valor "%s" aparece mas de una vez.',
                $repeated[0]
            ));
        }
    }

    /**
     * Busca un valor dentro de un rango de un recorrido.
     *
     * @param list<string> $values
     *
     * @return int Indice del valor o -1 si no esta en el rango.
     */
    protected function indexOf(array $values, string $value, int $start, int $end): int
    {
        for ($index = $start; $index < $end; $index++) {
            if ($values[$index] === $value) {
                return $index;
            }
        }

        return -1;
    }
}
