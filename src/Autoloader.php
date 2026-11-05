<?php

declare(strict_types=1);

namespace App;

/**
 * Autoloader PSR-4 minimalista.
 *
 * Traduce el namespace `App\Modulo\Clase` a la ruta `/src/Modulo/Clase.php`.
 * Se implementa propio para no depender de Composer ni de frameworks de terceros,
 * respetando la restriccion del master context.
 */
final class Autoloader
{
    private const PREFIX = 'App\\';

    private const BASE_DIR = __DIR__ . '/';

    /**
     * Registra el autoloader en la pila de PHP.
     */
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    /**
     * Resuelve y carga el archivo que corresponde a una clase.
     */
    public static function autoload(string $class): void
    {
        if (!str_starts_with($class, self::PREFIX)) {
            return;
        }

        $relativeClass = substr($class, strlen(self::PREFIX));
        $file = self::BASE_DIR . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
}
