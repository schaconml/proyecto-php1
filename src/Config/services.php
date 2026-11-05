<?php

declare(strict_types=1);

use App\Controllers\AcronymController;
use App\Controllers\BinaryController;
use App\Controllers\CalculatorController;
use App\Controllers\FibonacciFactorialController;
use App\Controllers\HomeController;
use App\Controllers\Request;
use App\Controllers\SetsController;
use App\Controllers\StatisticsController;
use App\Controllers\TreeController;
use App\Config\Container;
use App\Config\Database;
use App\Models\AcronymGenerator;
use App\Models\ArraySetOperations;
use App\Models\BinaryConverter;
use App\Models\CalculationOperation;
use App\Models\Calculator;
use App\Models\CalculatorHistoryRepository;
use App\Models\FibonacciGenerator;
use App\Models\FactorialGenerator;
use App\Models\InPostorderTreeBuilder;
use App\Models\NumberSeriesParser;
use App\Models\OperationSelector;
use App\Models\PreInorderTreeBuilder;
use App\Models\PrePostorderTreeBuilder;
use App\Models\SetOperations;
use App\Models\SetParser;
use App\Models\StatisticsCalculator;
use App\Models\TreeBuilderFactory;
use App\Models\TreeLayoutCalculator;
use App\Models\TraversalParser;
use App\Models\TreeTraverser;
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
    $container->set(StatisticsCalculator::class, new StatisticsCalculator());
    $container->set(NumberSeriesParser::class, new NumberSeriesParser());
    $container->set(Calculator::class, new Calculator());

    // El repositorio abre la conexion de forma perezosa: solo las aplicaciones
    // que necesitan la base de datos la abren, y si MySQL no esta disponible
    // el resto del sitio sigue funcionando.
    $container->setFactory(
        CalculatorHistoryRepository::class,
        static fn (): CalculatorHistoryRepository
            => new CalculatorHistoryRepository(Database::getConnection())
    );

    // Arbol binario: los reconstructions se eligen en tiempo de ejecucion.
    $container->set(TreeTraverser::class, new TreeTraverser());
    $container->set(TraversalParser::class, new TraversalParser());
    $container->set(TreeLayoutCalculator::class, new TreeLayoutCalculator());
    $container->set(PreInorderTreeBuilder::class, new PreInorderTreeBuilder());
    $container->set(PrePostorderTreeBuilder::class, new PrePostorderTreeBuilder());
    $container->set(InPostorderTreeBuilder::class, new InPostorderTreeBuilder());
    $container->set(
        TreeBuilderFactory::class,
        new TreeBuilderFactory(
            $container->get(PreInorderTreeBuilder::class),
            $container->get(PrePostorderTreeBuilder::class),
            $container->get(InPostorderTreeBuilder::class),
            $container->get(TreeTraverser::class)
        )
    );

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
    $container->set(
        TreeController::class,
        new TreeController(
            $request,
            $view,
            $container->get(TraversalParser::class),
            $container->get(TreeBuilderFactory::class),
            $container->get(TreeTraverser::class),
            $container->get(TreeLayoutCalculator::class)
        )
    );
    $container->set(
        StatisticsController::class,
        new StatisticsController(
            $request,
            $view,
            $container->get(StatisticsCalculator::class),
            $container->get(NumberSeriesParser::class)
        )
    );
    $container->set(
        CalculatorController::class,
        new CalculatorController(
            $request,
            $view,
            $container->get(Calculator::class),
            $container->get(CalculatorHistoryRepository::class)
        )
    );

    return $container;
};
