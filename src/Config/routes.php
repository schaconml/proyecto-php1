<?php

declare(strict_types=1);

use App\Controllers\AcronymController;
use App\Controllers\BinaryController;
use App\Controllers\HomeController;

/**
 * Mapa de rutas de la aplicacion.
 *
 * Formato: [METODO => [ruta => [Controlador::class, metodo]]].
 * Las rutas admiten marcadores de parametros en la forma {nombre}.
 */
return [
    'GET' => [
        '/'         => [HomeController::class, 'index'],
        '/acronimo' => [AcronymController::class, 'index'],
        '/binario'  => [BinaryController::class, 'index'],
    ],
    'POST' => [
        '/acronimo' => [AcronymController::class, 'store'],
        '/binario'  => [BinaryController::class, 'store'],
    ],
];
