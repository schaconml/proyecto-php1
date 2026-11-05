<?php

declare(strict_types=1);

namespace App\Config;

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

    /** @var array<string, callable(self): mixed> */
    private array $factories = [];

    /**
     * Registra una instancia bajo un identificador.
     */
    public function set(string $id, mixed $instance): void
    {
        $this->instances[$id] = $instance;
    }

    /**
     * Registra una fabrica perezosa bajo un identificador.
     *
     * La instancia se construye la primera vez que se solicita y queda
     * memorizada. Sirve para los objetos que dependen de recursos externos
     * (por ejemplo la conexion a la base de datos), de modo que solo se abren
     * cuando la aplicacion que los necesita se esta usando.
     *
     * @param callable(self): mixed $factory
     */
    public function setFactory(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || isset($this->factories[$id]);
    }

    /**
     * Resuelve una instancia registrada.
     */
    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->factories[$id])) {
            $instance = ($this->factories[$id])($this);
            $this->instances[$id] = $instance;

            return $instance;
        }

        throw new RuntimeException(sprintf('No hay ninguna instancia registrada para "%s".', $id));
    }
}
