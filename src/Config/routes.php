<?php

declare(strict_types=1);

use App\Controllers\HomeController;

/**
 * Mapa de rutas de la aplicacion.
 *
 * Formato: [METODO => [ruta => [Controlador::class, metodo]]].
 * Las rutas admiten marcadores de parametros en la forma {nombre}.
 */
return [
    'GET' => [
        '/' => [HomeController::class, 'index'],
    ],
];
