<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Validador de entrada reutilizable.
 *
 * Responsabilidad unica: comprobar las reglas de los datos que llegan por HTTP
 * y devolver los mensajes de error de la forma que la vista necesita.
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /**
     * Comprueba que el valor no este vacio.
     */
    public function required(string $field, mixed $value, string $label): self
    {
        if ($value === null) {
            return $this->addError($field, sprintf('El campo "%s" es obligatorio.', $label));
        }

        if (is_string($value) && trim($value) === '') {
            return $this->addError($field, sprintf('El campo "%s" es obligatorio.', $label));
        }

        if (is_array($value) && $value === []) {
            return $this->addError($field, sprintf('El campo "%s" es obligatorio.', $label));
        }

        return $this;
    }

    /**
     * Comprueba la longitud maxima de un texto.
     */
    public function maxLength(string $field, string $value, int $max, string $label): self
    {
        if (mb_strlen($value) > $max) {
            return $this->addError(
                $field,
                sprintf('El campo "%s" no puede superar los %d caracteres.', $label, $max)
            );
        }

        return $this;
    }

    /**
     * Comprueba que el texto no tenga mas palabras de las permitidas.
     */
    public function maxWords(string $field, string $value, int $max, string $label): self
    {
        $words = preg_split('/\s+/u', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($words) > $max) {
            return $this->addError(
                $field,
                sprintf('El campo "%s" admite maximo %d palabras.', $label, $max)
            );
        }

        return $this;
    }

    /**
     * Comprueba que el texto represente un numero entero (signo opcional).
     */
    public function integer(string $field, string $value, string $label): self
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return $this;
        }

        if (!preg_match('/^[+-]?\d+$/u', $trimmed)) {
            return $this->addError(
                $field,
                sprintf('El campo "%s" debe contener un numero entero.', $label)
            );
        }

        return $this;
    }

    /**
     * Comprueba que el entero esté dentro del rango indicado.
     *
     * La comparacion se hace sobre el texto para no perder precision con los
     * limites de un entero de 64 bits.
     */
    public function integerBetween(string $field, string $value, int $min, int $max, string $label): self
    {
        $trimmed = trim($value);

        if ($trimmed === '' || !preg_match('/^[+-]?\d+$/u', $trimmed)) {
            return $this;
        }

        $negative = str_starts_with($trimmed, '-');
        $digits = ltrim($negative ? substr($trimmed, 1) : $trimmed, '+');
        $digits = ltrim($digits, '0');

        if ($digits === '') {
            $digits = '0';
        }

        $limit = $negative
            ? ltrim((string) PHP_INT_MIN, '-')
            : (string) PHP_INT_MAX;

        if (self::compareDigits($digits, $limit) > 0) {
            return $this->addError(
                $field,
                sprintf('El campo "%s" debe estar entre %d y %d.', $label, $min, $max)
            );
        }

        return $this;
    }

    /**
     * Compara dos cadenas numericas positivas por longitud y luego lexicograficamente.
     */
    private static function compareDigits(string $digits, string $limit): int
    {
        if (strlen($digits) !== strlen($limit)) {
            return strlen($digits) <=> strlen($limit);
        }

        return strcmp($digits, $limit) <=> 0;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /**
     * @return array<string, string> Errores indexados por nombre de campo.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $message) {
            return $message;
        }

        return null;
    }

    private function addError(string $field, string $message): self
    {
        $this->errors[$field] ??= $message;

        return $this;
    }
}
