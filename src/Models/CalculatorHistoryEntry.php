<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Registro del historial de la calculadora.
 *
 * Objeto de valor inmutable: guarda una operacion tal como quedo almacenada en
 * la base de datos. Los importes se conservan como texto para no alterar el
 * formato con el que MySQL los devolvio.
 */
final class CalculatorHistoryEntry
{
    public function __construct(
        public readonly int $id,
        public readonly string $number1,
        public readonly string $operation,
        public readonly ?string $number2,
        public readonly string $result,
        public readonly string $registeredAt
    ) {
    }

    /**
     * Fecha en formato ISO 8601, apta para el atributo datetime del HTML.
     */
    public function isoDate(): string
    {
        return str_replace(' ', 'T', $this->registeredAt);
    }

    /**
     * Convierte el registro en los datos que necesita la vista.
     *
     * @return array{
     *     id: int,
     *     number1: string,
     *     operation: string,
     *     number2: string|null,
     *     result: string,
     *     registeredAt: string,
     *     isoDate: string
     * }
     */
    public function toRow(): array
    {
        return [
            'id'           => $this->id,
            'number1'      => $this->number1,
            'operation'    => CalculationOperation::label($this->operation),
            'number2'      => $this->number2,
            'result'       => $this->result,
            'registeredAt' => $this->registeredAt,
            'isoDate'      => $this->isoDate(),
        ];
    }
}
