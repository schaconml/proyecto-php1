<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Implementacion de las operaciones entre conjuntos sobre arrays de PHP.
 *
 * Los conjuntos llegan normalizados (sin repeticiones y ordenados), por lo que
 * basta con aplicar las funciones nativas de arrays y volver a construir el
 * resultado como conjunto.
 */
final class ArraySetOperations implements SetOperations
{
    public function union(IntegerSet $a, IntegerSet $b): IntegerSet
    {
        return IntegerSet::fromValues(array_merge($a->toArray(), $b->toArray()));
    }

    public function intersection(IntegerSet $a, IntegerSet $b): IntegerSet
    {
        return IntegerSet::fromValues(array_values(array_intersect($a->toArray(), $b->toArray())));
    }

    public function differenceAminusB(IntegerSet $a, IntegerSet $b): IntegerSet
    {
        return IntegerSet::fromValues(array_values(array_diff($a->toArray(), $b->toArray())));
    }

    public function differenceBminusA(IntegerSet $a, IntegerSet $b): IntegerSet
    {
        return IntegerSet::fromValues(array_values(array_diff($b->toArray(), $a->toArray())));
    }
}
