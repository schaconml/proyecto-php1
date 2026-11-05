<?php

declare(strict_types=1);

namespace App\Core;

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
