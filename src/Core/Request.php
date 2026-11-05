<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Envoltura inmutable de la peticion HTTP.
 *
 * Unica responsabilidad: exponer metodo, ruta y datos de entrada ya normalizados.
 */
final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query = [],
        private readonly array $post = []
    ) {
    }

    /**
     * Construye la peticion a partir de las superglobales de PHP.
     *
     * @param string $basePath   Raiz publica de la aplicacion (ej. '/proyecto-php1').
     * @param string $scriptName Ruta fisica del front controller (ej. '/proyecto-php1/public/index.php').
     */
    public static function fromGlobals(string $basePath, string $scriptName = ''): self
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $uri = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');

        return new self(
            $method,
            self::resolvePath($uri, $basePath, $scriptName),
            $_GET,
            $_POST
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return array<string, mixed>
     */
    public function query(): array
    {
        return $this->query;
    }

    /**
     * @return array<string, mixed>
     */
    public function post(): array
    {
        return $this->post;
    }

    public function queryParam(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function postParam(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Normaliza una ruta a formato '/segmento/segmento' (sin barra final).
     */
    private static function normalizePath(string $path): string
    {
        return '/' . trim($path, '/');
    }

    /**
     * Resuelve la ruta logica de la aplicacion a partir de la URI solicitada.
     *
     * Si la peticion entra por el front controller fisico se descarta su nombre;
     * en caso contrario se descarta el prefijo de instalacion.
     */
    private static function resolvePath(string $uri, string $basePath, string $scriptName): string
    {
        if ($scriptName !== '' && str_starts_with($uri, $scriptName)) {
            return self::normalizePath(substr($uri, strlen($scriptName)));
        }

        if ($basePath !== '' && $basePath !== '/') {
            if ($uri === $basePath) {
                return '/';
            }

            if (str_starts_with($uri, $basePath . '/')) {
                return self::normalizePath(substr($uri, strlen($basePath)));
            }
        }

        return self::normalizePath($uri);
    }
}
