<?php

declare(strict_types=1);

use App\Controllers\AcronymController;
use App\Controllers\HomeController;
use App\Core\Container;
use App\Core\Request;
use App\Core\View;
use App\Models\Acronym\AcronymGenerator;

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

    // Controladores: reciben por constructor lo que necesitan.
    $container->set(
        HomeController::class,
        new HomeController($request, $view)
    );
    $container->set(
        AcronymController::class,
        new AcronymController($request, $view, $container->get(AcronymGenerator::class))
    );

    return $container;
};
