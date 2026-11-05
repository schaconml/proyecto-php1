<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Convierte el texto digitado por el usuario en un conjunto de enteros.
 *
 * Responsable unica del formato de entrada: separa los valores, comprueba que
 * cada uno sea un entero y delega la normalizacion en IntegerSet.
 *
 * Se aceptan como separadores la coma, el punto y coma, los espacios y los
 * saltos de linea. Los valores repetidos se descartan y el conjunto queda
 * ordenado de forma ascendente.
 */
final class SetParser
{
    /**
     * Construye el conjunto a partir del texto recibido.
     *
     * @param string $raw   Texto con los valores del conjunto.
     * @param string $label Nombre del conjunto, usado en los mensajes de error.
     *
     * @throws InvalidArgumentException Si algun valor no es un entero.
     */
    public function parse(string $raw, string $label): IntegerSet
    {
        $tokens = preg_split('/[\s,;]+/u', trim($raw), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($tokens === []) {
            throw new InvalidArgumentException(
                sprintf('%s debe contener al menos un numero entero.', $label)
            );
        }

        $values = [];

        foreach ($tokens as $token) {
            if (!preg_match('/^[+-]?\d+$/u', $token)) {
                throw new InvalidArgumentException(
                    sprintf('%s contiene un valor no valido: "%s". Solo se admiten numeros enteros.', $label, $token)
                );
            }

            $values[] = (int) $token;
        }

        return IntegerSet::fromValues($values);
    }
}
