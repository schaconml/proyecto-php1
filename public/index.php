<?php

declare(strict_types=1);

/**
 * Front Controller de la aplicacion "Taller PHP".
 *
 * Responsabilidades (punto unico de composicion):
 *  1. Registrar el autoloader.
 *  2. Detectar la raiz publica de la instalacion (base path).
 *  3. Construir Request, View y Router.
 *  4. Delegar la peticion al Router.
 *
 * No contiene logica de negocio: eso vive en /src/Models.
 */

use App\Autoloader;
use App\Controllers\Request;
use App\Controllers\Router;
use App\Views\View;

require_once dirname(__DIR__) . '/src/Autoloader.php';

Autoloader::register();

/**
 * Raiz publica de la instalacion.
 *
 * Con Apache + XAMPP el DocumentRoot es 'htdocs', por lo que el front controller
 * se ejecuta como '/proyecto-php1/public/index.php'. Se elimina el sufijo '/public'
 * para obtener la raiz logica de la aplicacion ('/proyecto-php1').
 */
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
$scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$basePath = (string) preg_replace('#/public$#', '', $scriptDir);

if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}

$request = Request::fromGlobals($basePath, $scriptName);
$view = new View($basePath);

try {
    /** @var App\Config\Container $container */
    $container = (require dirname(__DIR__) . '/src/Config/services.php')($request, $view);

    $router = new Router($view, $container);
    $router->load(require dirname(__DIR__) . '/src/Config/routes.php');

    $router->dispatch($request);
} catch (Throwable $exception) {
    // Cualquier error inesperado se muestra como una pagina 500 con un
    // mensaje seguro; el detalle tecnico queda en el registro del servidor.
    http_response_code(500);
    error_log($exception->getMessage());

    // Si el problema es la base de datos, el mensaje orienta al usuario.
    $message = str_contains($exception->getMessage(), 'base de datos')
        ? 'No fue posible conectar con la base de datos. Verifique que MySQL este en ejecucion '
            . 'y que se haya importado el archivo database.sql.'
        : 'No fue posible completar la operacion. Intente nuevamente en unos momentos.';

    echo $view->render('error-500', [
        'message' => $message,
    ], 'Error');
}
