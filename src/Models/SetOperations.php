<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Operaciones entre dos conjuntos de enteros.
 *
 * Cada metodo resuelve una operacion distinta, por lo que la interfaz es
 * pequena y cohesionada (ISP). Anadir una operacion nueva, como la diferencia
 * simetrica, no obliga a modificar el codigo existente (OCP).
 */
interface SetOperations
{
    /**
     * Elementos que pertenecen a A o a B.
     */
    public function union(IntegerSet $a, IntegerSet $b): IntegerSet;

    /**
     * Elementos que pertenecen a A y a B.
     */
    public function intersection(IntegerSet $a, IntegerSet $b): IntegerSet;

    /**
     * Elementos que pertenecen a A y no a B (A - B).
     */
    public function differenceAminusB(IntegerSet $a, IntegerSet $b): IntegerSet;

    /**
     * Elementos que pertenecen a B y no a A (B - A).
     */
    public function differenceBminusA(IntegerSet $a, IntegerSet $b): IntegerSet;
}
