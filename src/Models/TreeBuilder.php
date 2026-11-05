<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Contrato de los reconstructores de arboles binarios.
 *
 * Cada implementacion sabe reconstruir el arbol a partir de un par de
 * recorridos concreto. Gracias a esta interfaz se puede anadir un par nuevo
 * (por ejemplo preorden e inorden) sin modificar el codigo existente (OCP), y
 * el factory decide que implementacion se usa en cada caso (DIP).
 */
interface TreeBuilder
{
    /**
     * Reconstruye el arbol a partir de dos recorridos.
     *
     * @param list<string> $first  Primer recorrido (preorden, inorden o postorden).
     * @param list<string> $second Segundo recorrido.
     *
     * @throws \InvalidArgumentException Si los recorridos son inconsistentes.
     */
    public function build(array $first, array $second): BinaryTree;
}
