<?php

declare(strict_types=1);

use App\Controllers\AcronymController;
use App\Controllers\BinaryController;
use App\Controllers\FibonacciFactorialController;
use App\Controllers\HomeController;
use App\Controllers\Request;
use App\Controllers\SetsController;
use App\Config\Container;
use App\Models\AcronymGenerator;
use App\Models\ArraySetOperations;
use App\Models\BinaryConverter;
use App\Models\FibonacciGenerator;
use App\Models\FactorialGenerator;
use App\Models\OperationSelector;
use App\Models\SetOperations;
use App\Models\SetParser;
use App\Views\View;

/**
 * Punto unico de composicion: aqui se construyen los objetos y se inyectan
 * sus dependencias (patrones Dependency Injection e Inversion de Dependencias).
 */
return static function (Request $request, View $view): Container {
    $container = new Container();

    $container->set(Request::class, $request);
    $container->set(View::class, $view);

    // Modelos: no dependen de nada externo, se pueden instanciar directamente.
    $container->set(AcronymGenerator::class, new AcronymGenerator());
    $container->set(BinaryConverter::class, new BinaryConverter());
    $container->set(FibonacciGenerator::class, new FibonacciGenerator());
    $container->set(FactorialGenerator::class, new FactorialGenerator());
    $container->set(
        OperationSelector::class,
        new OperationSelector(
            $container->get(FibonacciGenerator::class),
            $container->get(FactorialGenerator::class)
        )
    );
    $container->set(SetParser::class, new SetParser());
    $container->set(SetOperations::class, new ArraySetOperations());

    // Controladores: reciben por constructor lo que necesitan.
    $container->set(
        HomeController::class,
        new HomeController($request, $view)
    );
    $container->set(
        AcronymController::class,
        new AcronymController($request, $view, $container->get(AcronymGenerator::class))
    );
    $container->set(
        BinaryController::class,
        new BinaryController($request, $view, $container->get(BinaryConverter::class))
    );
    $container->set(
        FibonacciFactorialController::class,
        new FibonacciFactorialController($request, $view, $container->get(OperationSelector::class))
    );
    $container->set(
        SetsController::class,
        new SetsController(
            $request,
            $view,
            $container->get(SetParser::class),
            $container->get(SetOperations::class)
        )
    );

    return $container;
};
