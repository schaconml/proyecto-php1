<?php

declare(strict_types=1);

namespace App\Models;

use DivisionByZeroError;
use InvalidArgumentException;

/**
 * Calculadora de operaciones basicas.
 *
 * Responsabilidad unica: resolver la operacion solicitada. No guarda nada en
 * la base de datos ni depende de HTTP.
 *
 * El porcentaje se calcula con la convencion "signo y magnitud" acordada: el
 * primer numero es la base y el segundo es el porcentaje que se aplica
 * (por ejemplo, base 200 y 15% equivalen a 30).
 */
final class Calculator
{
    /**
     * Ejecuta la operacion indicada.
     *
     * @param float       $number1  Primer operando (base en el caso del porcentaje).
     * @param string      $operation Identificador de la operacion.
     * @param float|null  $number2  Segundo operando (porcentaje en el caso del porcentaje).
     *
     * @throws InvalidArgumentException Si la operacion no existe.
     * @throws DivisionByZeroError      Si se intenta dividir entre cero.
     */
    public function calculate(float $number1, string $operation, ?float $number2 = null): float
    {
        if (!CalculationOperation::isValid($operation)) {
            throw new InvalidArgumentException(sprintf('La operacion "%s" no existe.', $operation));
        }

        $second = $number2 ?? 0.0;

        return match ($operation) {
            CalculationOperation::SUMA           => $number1 + $second,
            CalculationOperation::RESTA          => $number1 - $second,
            CalculationOperation::MULTIPLICACION => $number1 * $second,
            CalculationOperation::DIVISION       => $this->divide($number1, $second),
            CalculationOperation::PORCENTAJE     => $this->percentage($number1, $second),
            default => throw new InvalidArgumentException(
                sprintf('La operacion "%s" no existe.', $operation)
            ),
        };
    }

    /**
     * Division exacta: el divisor no puede ser cero.
     */
    private function divide(float $number1, float $number2): float
    {
        if ($number2 == 0.0) {
            throw new DivisionByZeroError('No se puede dividir entre cero.');
        }

        return $number1 / $number2;
    }

    /**
     * Porcentaje: la base multiplicada por el porcentaje dividido entre 100.
     */
    private function percentage(float $base, float $percent): float
    {
        return ($base * $percent) / 100;
    }
}
