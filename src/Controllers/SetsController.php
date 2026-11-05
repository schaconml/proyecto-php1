<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SetOperations;
use App\Models\SetParser;
use App\Models\Validator;
use App\Views\View;
use InvalidArgumentException;

/**
 * Controlador de la aplicacion de operaciones entre conjuntos.
 *
 * Responsabilidad unica: leer los dos conjuntos, delegar el analisis en el
 * parser y el calculo en las operaciones, y preparar los datos de la vista.
 * No contiene logica de conjuntos.
 */
final class SetsController
{
    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly SetParser $parser,
        private readonly SetOperations $operations
    ) {
    }

    /**
     * Muestra el formulario con los dos conjuntos.
     */
    public function index(): string
    {
        return $this->view->render('sets', [
            'setA'    => '',
            'setB'    => '',
            'results' => [],
            'errors'  => [],
        ], 'Conjuntos');
    }

    /**
     * Calcula la union, la interseccion y las dos diferencias.
     */
    public function store(): string
    {
        $rawA = (string) $this->request->postParam('setA', '');
        $rawB = (string) $this->request->postParam('setB', '');

        $errors = [];
        $results = [];

        $validator = new Validator();
        $validator->required('setA', $rawA, 'El conjunto A');
        $validator->required('setB', $rawB, 'El conjunto B');

        if ($validator->passes()) {
            try {
                $setA = $this->parser->parse($rawA, 'El conjunto A');
            } catch (InvalidArgumentException $exception) {
                $errors['setA'] = $exception->getMessage();
                $setA = null;
            }

            try {
                $setB = $this->parser->parse($rawB, 'El conjunto B');
            } catch (InvalidArgumentException $exception) {
                $errors['setB'] = $exception->getMessage();
                $setB = null;
            }

            if ($setA !== null && $setB !== null) {
                $results = [
                    'union'        => $this->operations->union($setA, $setB),
                    'intersection' => $this->operations->intersection($setA, $setB),
                    'differenceAB' => $this->operations->differenceAminusB($setA, $setB),
                    'differenceBA' => $this->operations->differenceBminusA($setA, $setB),
                ];
            }
        } else {
            $errors = $validator->errors();
        }

        return $this->view->render('sets', [
            'setA'    => $rawA,
            'setB'    => $rawB,
            'results' => $results,
            'errors'  => $errors,
        ], 'Conjuntos');
    }
}
