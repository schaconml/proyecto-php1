<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BinaryConverter;
use App\Models\Validator;
use App\Views\View;

/**
 * Controlador de la aplicacion de conversion a binario.
 *
 * Responsabilidad unica: leer el numero, delegar la conversion en el modelo
 * y preparar los datos de la vista. No contiene el algoritmo de conversion.
 */
final class BinaryController
{
    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly BinaryConverter $converter
    ) {
    }

    /**
     * Muestra el formulario de conversion.
     */
    public function index(): string
    {
        return $this->view->render('binary', [
            'number'  => '',
            'binary'  => null,
            'decimal' => null,
            'bits'    => null,
            'errors'  => [],
        ], 'Binario');
    }

    /**
     * Convierte el numero enviado y muestra el resultado.
     */
    public function store(): string
    {
        $raw = (string) $this->request->postParam('number', '');
        $errors = [];
        $binary = null;
        $decimal = null;
        $bits = null;

        $validator = new Validator();
        $validator->required('number', $raw, 'El numero');
        $validator->integer('number', $raw, 'El numero');
        $validator->integerBetween('number', $raw, PHP_INT_MIN, PHP_INT_MAX, 'El numero');

        if ($validator->fails()) {
            $errors = $validator->errors();
        } else {
            $number = (int) trim($raw);
            $binary = $this->converter->toBinary($number);
            $decimal = $number;
            $bits = $this->converter->bitLength($number);
        }

        return $this->view->render('binary', [
            'number'  => $raw,
            'binary'  => $binary,
            'decimal' => $decimal,
            'bits'    => $bits,
            'errors'  => $errors,
        ], 'Binario');
    }
}
