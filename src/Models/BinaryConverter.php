<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Conversor de numeros enteros a representacion binaria.
 *
 * Responsabilidad unica: convertir entre entero decimal y cadena binaria.
 * No depende de HTTP, de la base de datos ni de la vista.
 *
 * El algoritmo es el clasico de divisiones sucesivas entre 2: se toma el resto
 * de cada division (que siempre es 0 o 1) y los restos se leen de abajo hacia
 * arriba para reconstruir el numero.
 *
 * Para los negativos se usa la convencion de signo y magnitud (por ejemplo
 * -5 se representa como "-101"), no el complemento a dos.
 */
final class BinaryConverter
{
    /**
     * Convierte un entero en su representacion binaria.
     */
    public function toBinary(int $number): string
    {
        if ($number === 0) {
            return '0';
        }

        $negative = $number < 0;

        // PHP_INT_MIN no tiene un valor absoluto representable. Su magnitud es
        // 2^(PHP_INT_SIZE * 8 - 1), es decir, un 1 seguido de los ceros que
        // correspondan al ancho del entero.
        if ($negative && $number === PHP_INT_MIN) {
            return '-1' . str_repeat('0', PHP_INT_SIZE * 8 - 1);
        }

        $magnitude = abs($number);
        $digits = [];

        while ($magnitude > 0) {
            $digits[] = (string) ($magnitude % 2);
            $magnitude = intdiv($magnitude, 2);
        }

        $binary = implode('', array_reverse($digits));

        return $negative ? '-' . $binary : $binary;
    }

    /**
     * Convierte una cadena binaria en su valor entero decimal.
     *
     * @param string $binary Cadena de unos y ceros, con signo opcional.
     */
    public function toDecimal(string $binary): int
    {
        $binary = trim($binary);

        if ($binary === '') {
            return 0;
        }

        $negative = str_starts_with($binary, '-');

        if ($negative) {
            $binary = substr($binary, 1);
        }

        $decimal = 0;

        foreach (str_split($binary) as $digit) {
            $decimal = $decimal * 2 + (int) $digit;
        }

        return $negative ? -$decimal : $decimal;
    }

    /**
     * Numero de bits de la representacion binaria (sin contar el signo).
     */
    public function bitLength(int $number): int
    {
        return strlen(ltrim($this->toBinary($number), '-'));
    }
}
