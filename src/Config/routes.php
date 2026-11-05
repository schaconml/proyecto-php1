<?php

declare(strict_types=1);

use App\Controllers\AcronymController;
use App\Controllers\BinaryController;
use App\Controllers\CalculatorController;
use App\Controllers\FibonacciFactorialController;
use App\Controllers\HomeController;
use App\Controllers\SetsController;
use App\Controllers\StatisticsController;
use App\Controllers\TreeController;

/**
 * Mapa de rutas de la aplicacion.
 *
 * Formato: [METODO => [ruta => [Controlador::class, metodo]]].
 * Las rutas admiten marcadores de parametros en la forma {nombre}.
 */
return [
    'GET' => [
        '/'                     => [HomeController::class, 'index'],
        '/acronimo'             => [AcronymController::class, 'index'],
        '/binario'              => [BinaryController::class, 'index'],
        '/fibonacci-factorial'  => [FibonacciFactorialController::class, 'index'],
        '/conjuntos'            => [SetsController::class, 'index'],
        '/arbol'               => [TreeController::class, 'index'],
        '/estadisticas'         => [StatisticsController::class, 'index'],
        '/calculadora'          => [CalculatorController::class, 'index'],
    ],
    'POST' => [
        '/acronimo'             => [AcronymController::class, 'store'],
        '/binario'              => [BinaryController::class, 'store'],
        '/fibonacci-factorial'  => [FibonacciFactorialController::class, 'store'],
        '/conjuntos'            => [SetsController::class, 'store'],
        '/arbol'               => [TreeController::class, 'store'],
        '/estadisticas'         => [StatisticsController::class, 'store'],
        '/calculadora'          => [CalculatorController::class, 'store'],
        '/calculadora/historial/borrar'      => [CalculatorController::class, 'clearHistory'],
        '/calculadora/historial/borrar/{id}' => [CalculatorController::class, 'deleteEntry'],
    ],
];
