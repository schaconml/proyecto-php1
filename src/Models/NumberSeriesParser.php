<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Convierte los valores digitados por el usuario en una serie de numeros.
 *
 * Responsable unica del formato de entrada: normaliza cada valor (admite la
 * coma decimal) y comprueba que sea un numero real valido.
 */
final class NumberSeriesParser
{
    /**
     * Analiza la lista de valores ingresada en el formulario.
     *
     * @param list<string> $values
     *
     * @return list<float>
     *
     * @throws InvalidArgumentException Si algun valor no es un numero.
     */
    public function parseList(array $values): array
    {
        $numbers = [];

        foreach ($values as $position => $raw) {
            $normalized = $this->normalize((string) $raw);

            if ($normalized === '') {
                throw new InvalidArgumentException(sprintf(
                    'El numero %d esta vacio.',
                    $position + 1
                ));
            }

            if (!preg_match('/^[+-]?\d+(\.\d+)?$/u', $normalized)) {
                throw new InvalidArgumentException(sprintf(
                    'El valor "%s" no es un numero valido.',
                    trim((string) $raw)
                ));
            }

            $numbers[] = (float) $normalized;
        }

        return $numbers;
    }

    /**
     * Normaliza un numero: quita espacios y cambia la coma decimal por punto.
     */
    private function normalize(string $value): string
    {
        return str_replace(',', '.', trim($value));
    }
}
