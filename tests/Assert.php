<?php

declare(strict_types=1);

namespace Tests;

/**
 * Aserciones minimas para las pruebas.
 *
 * Evita depender de PHPUnit para mantener el proyecto autocontenido.
 */
final class Assert
{
    /** @var list<string> */
    private array $failures = [];

    private int $total = 0;

    public function equals(mixed $expected, mixed $actual, string $message): void
    {
        $this->total++;

        if ($expected !== $actual) {
            $this->failures[] = sprintf(
                '%s | esperado: %s | obtenido: %s',
                $message,
                var_export($expected, true),
                var_export($actual, true)
            );
        }
    }

    public function true(bool $condition, string $message): void
    {
        $this->total++;

        if (!$condition) {
            $this->failures[] = sprintf('%s | se esperaba true', $message);
        }
    }

    public function false(bool $condition, string $message): void
    {
        $this->true(!$condition, $message);
    }

    /**
     * @return list<string>
     */
    public function failures(): array
    {
        return $this->failures;
    }

    public function total(): int
    {
        return $this->total;
    }
}
