<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Selector de la operacion matematica solicitada por el usuario.
 *
 * Responsabilidad unica: traducir el identificador de la operacion (el valor
 * que llega por HTTP) en el generador que la implementa. Los generadores se
 * inyectan por constructor, de modo que esta clase no construye ninguno.
 */
final class OperationSelector
{
    public const FIBONACCI = 'fibonacci';

    public const FACTORIAL = 'factorial';

    public function __construct(
        private readonly FibonacciGenerator $fibonacci,
        private readonly FactorialGenerator $factorial
    ) {
    }

    /**
     * Operaciones disponibles en el formulario.
     *
     * @return list<string>
     */
    public static function available(): array
    {
        return [self::FIBONACCI, self::FACTORIAL];
    }

    /**
     * Indica si el identificador corresponde a una operacion valida.
     */
    public static function isValid(string $operation): bool
    {
        return in_array($operation, self::available(), true);
    }

    /**
     * Devuelve el generador de la operacion indicada.
     */
    public function select(string $operation): SequenceGenerator
    {
        return match ($operation) {
            self::FIBONACCI => $this->fibonacci,
            self::FACTORIAL => $this->factorial,
            default => throw new InvalidArgumentException(
                sprintf('Operacion desconocida: %s', $operation)
            ),
        };
    }
}
