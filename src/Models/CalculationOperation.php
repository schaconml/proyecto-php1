<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Catalogo de las operaciones de la calculadora.
 *
 * Responsabilidad unica: definir las operaciones validas, sus identificadores
 * (los que se guardan en la base de datos) y su etiqueta para la interfaz.
 */
final class CalculationOperation
{
    public const SUMA = 'suma';

    public const RESTA = 'resta';

    public const MULTIPLICACION = 'multiplicacion';

    public const DIVISION = 'division';

    public const PORCENTAJE = 'porcentaje';

    /**
     * Operaciones disponibles, en el orden en que se muestran.
     *
     * @return list<string>
     */
    public static function available(): array
    {
        return [
            self::SUMA,
            self::RESTA,
            self::MULTIPLICACION,
            self::DIVISION,
            self::PORCENTAJE,
        ];
    }

    /**
     * Indica si el identificador corresponde a una operacion valida.
     */
    public static function isValid(string $operation): bool
    {
        return in_array($operation, self::available(), true);
    }

    /**
     * Etiqueta legible de la operacion.
     */
    public static function label(string $operation): string
    {
        return match ($operation) {
            self::SUMA           => 'Suma',
            self::RESTA          => 'Resta',
            self::MULTIPLICACION => 'Multiplicación',
            self::DIVISION       => 'División',
            self::PORCENTAJE     => 'Porcentaje',
            default              => $operation,
        };
    }
}
