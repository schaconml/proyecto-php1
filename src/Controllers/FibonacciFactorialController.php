<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OperationSelector;
use App\Models\Validator;
use App\Views\View;

/**
 * Controlador de la aplicacion de sucesion de Fibonacci y factorial.
 *
 * Responsabilidad unica: leer el numero y la operacion elegida, delegar el
 * calculo en el modelo correspondiente y preparar los datos de la vista.
 * No contiene ningun algoritmo matematico.
 */
final class FibonacciFactorialController
{
    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly OperationSelector $selector
    ) {
    }

    /**
     * Muestra el formulario con el selector de operacion.
     */
    public function index(): string
    {
        return $this->view->render('fibonacci-factorial', [
            'number'    => '',
            'operation' => OperationSelector::FIBONACCI,
            'terms'     => [],
            'factorial' => null,
            'errors'    => [],
        ], 'Fibonacci y Factorial');
    }

    /**
     * Calcula la secuencia o el factorial solicitado.
     */
    public function store(): string
    {
        $rawNumber = (string) $this->request->postParam('number', '');
        $operation = (string) $this->request->postParam('operation', OperationSelector::FIBONACCI);

        $errors = [];
        $terms = [];
        $factorial = null;

        $validator = new Validator();
        $validator->required('operation', $operation, 'La operacion');
        $validator->required('number', $rawNumber, 'El numero');
        $validator->integer('number', $rawNumber, 'El numero');

        if (!OperationSelector::isValid($operation)) {
            $validator->error('operation', 'La operacion seleccionada no es valida.');
        }

        if ($validator->passes()) {
            $generator = $this->selector->select($operation);
            $validator->integerBetween(
                'number',
                $rawNumber,
                $generator->minInput(),
                $generator->maxInput(),
                'El numero'
            );
        }

        if ($validator->fails()) {
            $errors = $validator->errors();
        } else {
            $generator = $this->selector->select($operation);
            $number = (int) trim($rawNumber);
            $result = $generator->generate($number);

            if ($operation === OperationSelector::FIBONACCI) {
                $terms = array_map('strval', $result);
            } else {
                $factorial = (string) $result[0];
            }
        }

        return $this->view->render('fibonacci-factorial', [
            'number'    => $rawNumber,
            'operation' => $operation,
            'terms'     => $terms,
            'factorial' => $factorial,
            'errors'    => $errors,
        ], 'Fibonacci y Factorial');
    }
}
