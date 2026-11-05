<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Renderizador de plantillas.
 *
 * Unica responsabilidad: cargar plantillas PHP, componer el layout y escapar la salida.
 * El escapado se centraliza en e() para cumplir la regla de seguridad del proyecto.
 */
final class View
{
    private readonly string $viewsPath;

    public function __construct(private readonly string $basePath = '')
    {
        $this->viewsPath = dirname(__DIR__) . '/Views/';
    }

    /**
     * Renderiza una plantilla dentro del layout principal.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], string $title = 'Taller PHP'): string
    {
        $content = $this->partial($template, $data);

        return $this->partial('layouts/main', array_merge($data, [
            'content' => $content,
            'title'   => $title,
        ]));
    }

    /**
     * Renderiza una plantilla suelta sin layout.
     *
     * @param array<string, mixed> $data
     */
    public function partial(string $template, array $data = []): string
    {
        $file = $this->viewsPath . str_replace('/', DIRECTORY_SEPARATOR, $template) . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException(sprintf('Plantilla no encontrada: %s', $template));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }

    /**
     * Escapa un valor para imprimirlo en HTML.
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Construye una URL interna de la aplicacion.
     */
    public function url(string $path = ''): string
    {
        $path = trim($path, '/');

        if ($path === '') {
            return $this->basePath === '' ? '/' : $this->basePath . '/';
        }

        return $this->basePath . '/' . $path;
    }

    /**
     * URL de un recurso estatico (CSS, imagenes) servido desde /public.
     */
    public function asset(string $path): string
    {
        return $this->url('public/' . ltrim($path, '/'));
    }
}
