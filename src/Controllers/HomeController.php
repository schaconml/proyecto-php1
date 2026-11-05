<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\View;

/**
 * Controlador de la pagina principal (menu del taller).
 *
 * Responsabilidad unica: preparar la peticion y delegar el renderizado a la vista.
 * No contiene logica matematica ni consultas SQL.
 */
final class HomeController
{
    public function __construct(
        private readonly Request $request,
        private readonly View $view
    ) {
    }

    public function index(): string
    {
        return $this->view->render('home', [
            'modules' => $this->modules(),
        ], 'Taller PHP');
    }

    /**
     * Catalogo de las 7 aplicaciones del taller.
     *
     * @return list<array{number: int, name: string, description: string, slug: string|null, ready: bool}>
     */
    private function modules(): array
    {
        return [
            [
                'number'      => 1,
                'name'        => 'Acronimo',
                'description' => 'Convierte una frase en su acronimo. Los guiones separan palabras y el resto de la puntuacion se elimina.',
                'slug'        => 'acronimo',
                'ready'       => true,
            ],
            [
                'number'      => 2,
                'name'        => 'Fibonacci y Factorial',
                'description' => 'Calcula la sucesion de Fibonacci o el factorial del numero ingresado mediante un formulario con selector de operacion.',
                'slug'        => 'fibonacci-factorial',
                'ready'       => true,
            ],
            [
                'number'      => 3,
                'name'        => 'Estadisticas',
                'description' => 'Calcula promedio, media (mediana) y moda de una serie de numeros reales cuya cantidad elige el usuario.',
                'slug'        => 'estadisticas',
                'ready'       => true,
            ],
            [
                'number'      => 4,
                'name'        => 'Conjuntos',
                'description' => 'Operaciones entre dos conjuntos de enteros: union, interseccion y las diferencias A-B y B-A.',
                'slug'        => 'conjuntos',
                'ready'       => true,
            ],
            [
                'number'      => 5,
                'name'        => 'Binario',
                'description' => 'Convierte un numero entero a su representacion binaria.',
                'slug'        => 'binario',
                'ready'       => true,
            ],
            [
                'number'      => 6,
                'name'        => 'Arbol binario',
                'description' => 'Construye un arbol binario a partir de dos o tres recorridos: preorden, inorden y postorden.',
                'slug'        => 'arbol',
                'ready'       => true,
            ],
            [
                'number'      => 7,
                'name'        => 'Calculadora',
                'description' => 'Calculadora con suma, resta, multiplicacion, division y porcentaje, con historial almacenado en MySQL.',
                'slug'        => null,
                'ready'       => false,
            ],
        ];
    }
}
