<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Container;
use App\Views\View;

/**
 * Enrutador HTTP.
 *
 * Unica responsabilidad: resolver metodo + ruta y despachar al controlador
 * correspondiente. No contiene logica de negocio ni de presentacion.
 */
final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = [];

    public function __construct(
        private readonly View $view,
        private readonly Container $container
    ) {
    }

    /**
     * Carga el mapa de rutas definido en src/Config/routes.php.
     *
     * @param array<string, array<string, array{0: class-string, 1: string}>> $routes
     */
    public function load(array $routes): void
    {
        foreach ($routes as $method => $paths) {
            foreach ($paths as $path => $handler) {
                $this->add((string) $method, (string) $path, $handler);
            }
        }
    }

    /**
     * Registra una ruta.
     *
     * @param array{0: class-string, 1: string} $handler
     */
    public function add(string $method, string $path, array $handler): void
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = $handler;
    }

    /**
     * Resuelve la peticion y ejecuta el controlador.
     */
    public function dispatch(Request $request): void
    {
        $method = $request->method();

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $parameters = $this->match($pattern, $request->path());

            if ($parameters === null) {
                continue;
            }

            [$controllerClass, $action] = $handler;

            /** @var object $controller */
            $controller = $this->container->get($controllerClass);

            echo $controller->{$action}(...$parameters);

            return;
        }

        $this->notFound();
    }

    /**
     * @return list<string>|null Parametros de la ruta o null si no coincide.
     */
    private function match(string $pattern, string $path): ?array
    {
        $patternParts = explode('/', trim($pattern, '/'));
        $pathParts = explode('/', trim($path, '/'));

        if (count($patternParts) !== count($pathParts)) {
            return null;
        }

        $parameters = [];

        foreach ($patternParts as $index => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $parameters[] = $pathParts[$index];
                continue;
            }

            if ($part !== $pathParts[$index]) {
                return null;
            }
        }

        return $parameters;
    }

    private function notFound(): void
    {
        http_response_code(404);

        echo $this->view->render('error-404', [], '404 - Pagina no encontrada');
    }

    private function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }
}
