<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Contenedor de dependencias minimo.
 *
 * Permite construir los controladores en el punto unico de composicion
 * (public/index.php) e inyectarles sus dependencias, de modo que las clases
 * de dominio no construyan sus propias colaboradores (principio DIP).
 */
final class Container
{
    /** @var array<string, mixed> */
    private array $instances = [];

    /**
     * Registra una instancia bajo un identificador.
     */
    public function set(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * Resuelve una instancia registrada.
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new RuntimeException(sprintf('No hay ninguna instancia registrada para "%s".', $id));
        }

        return $this->instances[$id];
    }
}
