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
use App\Core\Request;
use App\Core\Router;
use App\Core\View;

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

/** @var App\Core\Container $container */
$container = (require dirname(__DIR__) . '/src/Config/services.php')($request, $view);

$router = new Router($view, $container);
$router->load(require dirname(__DIR__) . '/src/Config/routes.php');

$router->dispatch($request);
