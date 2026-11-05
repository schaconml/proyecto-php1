<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Validator;
use App\Core\View;
use App\Models\Acronym\AcronymGenerator;

/**
 * Controlador de la aplicacion de acronimos.
 *
 * Responsabilidad unica: leer la entrada, delegar la conversion en el modelo
 * y preparar los datos de la vista. No contiene logica de conversion.
 */
final class AcronymController
{
    private const MAX_LENGTH = 500;

    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly AcronymGenerator $generator
    ) {
    }

    /**
     * Muestra el formulario de conversion.
     */
    public function index(): string
    {
        return $this->view->render('acronym/index', [
            'phrase'  => '',
            'acronym' => null,
            'errors'  => [],
        ], 'Acronimo');
    }

    /**
     * Procesa la frase enviada y muestra el resultado.
     */
    public function store(): string
    {
        $phrase = (string) $this->request->postParam('phrase', '');
        $errors = [];
        $acronym = null;

        $validator = new Validator();
        $validator->required('phrase', $phrase, 'La frase');
        $validator->maxLength('phrase', $phrase, self::MAX_LENGTH, 'La frase');

        if ($validator->fails()) {
            $errors = $validator->errors();
        } else {
            $acronym = $this->generator->generate($phrase);

            if ($acronym === '') {
                $errors['phrase'] = 'La frase no contiene palabras que se puedan convertir en acronimo.';
            }
        }

        return $this->view->render('acronym/index', [
            'phrase'  => $phrase,
            'acronym' => $acronym,
            'errors'  => $errors,
        ], 'Acronimo');
    }
}
