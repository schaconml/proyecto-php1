<?php

declare(strict_types=1);

namespace App\Models;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Conjunto de numeros enteros (objeto de valor).
 *
 * Responsable unico de representar un conjunto. Garantiza su invariante:
 * los elementos no se repiten y estan ordenados de forma ascendente, por lo
 * que dos conjuntos con los mismos elementos son siempre iguales.
 *
 * No puede construirse en un estado invalido: la unica via de creacion es
 * fromValues(), que aplica la normalizacion.
 *
 * @implements IteratorAggregate<int, int>
 */
final class IntegerSet implements Countable, IteratorAggregate
{
    /**
     * @param list<int> $values Elementos unicos y ordenados de forma ascendente.
     */
    private function __construct(private readonly array $values)
    {
    }

    /**
     * Crea un conjunto a partir de una lista de enteros.
     *
     * @param list<int> $values
     */
    public static function fromValues(array $values): self
    {
        $unique = array_values(array_unique($values));
        sort($unique, SORT_NUMERIC);

        return new self($unique);
    }

    /**
     * Crea un conjunto vacio.
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @return list<int>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function size(): int
    {
        return count($this->values);
    }

    public function isEmpty(): bool
    {
        return $this->values === [];
    }

    public function contains(int $value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * Texto del conjunto con los elementos separados por comas.
     */
    public function __toString(): string
    {
        return implode(', ', $this->values);
    }

    public function count(): int
    {
        return $this->size();
    }

    /**
     * @return Traversable<int, int>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }
}
