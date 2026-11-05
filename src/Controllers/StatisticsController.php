<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\NumberSeriesParser;
use App\Models\StatisticsCalculator;
use App\Models\Validator;
use App\Views\View;
use InvalidArgumentException;

/**
 * Controlador de la aplicacion de estadisticas.
 *
 * El usuario decide cuantos numeros va a ingresar, por lo que la aplicacion
 * trabaja en dos pasos que se resuelven en el servidor (sin JavaScript):
 *  1. Se indica la cantidad de numeros y la aplicacion genera los campos.
 *  2. Se diligencian los campos y la aplicacion calcula las medidas.
 *
 * Responsabilidad unica: coordinar los dos pasos, delegar el analisis en el
 * parser y el calculo en el calculador, y preparar los datos de la vista.
 */
final class StatisticsController
{
    private const MIN_COUNT = 1;

    private const MAX_COUNT = 100;

    private const PRECISION = 6;

    private const STEP_AMOUNT = 'amount';

    private const STEP_STATISTICS = 'statistics';

    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly StatisticsCalculator $calculator,
        private readonly NumberSeriesParser $parser
    ) {
    }

    /**
     * Muestra el formulario inicial con la cantidad de numeros.
     */
    public function index(): string
    {
        $count = (int) $this->request->queryParam('count', '0');

        if ($count < self::MIN_COUNT || $count > self::MAX_COUNT) {
            $count = 0;
        }

        return $this->view->render('statistics', [
            'count'   => $count,
            'numbers' => array_fill(0, $count, ''),
            'results' => null,
            'errors'  => [],
        ], 'Estadisticas');
    }

    /**
     * Procesa el paso enviado: generar los campos o calcular las medidas.
     */
    public function store(): string
    {
        $step = (string) $this->request->postParam('step', self::STEP_STATISTICS);

        if ($step === self::STEP_AMOUNT) {
            return $this->showAmountStep();
        }

        return $this->calculateStatistics();
    }

    /**
     * Primer paso: valida la cantidad y muestra los campos para los numeros.
     */
    private function showAmountStep(): string
    {
        $rawCount = (string) $this->request->postParam('count', '');

        $validator = new Validator();
        $validator->required('count', $rawCount, 'La cantidad de numeros');
        $validator->integer('count', $rawCount, 'La cantidad de numeros');
        $validator->integerBetween(
            'count',
            $rawCount,
            self::MIN_COUNT,
            self::MAX_COUNT,
            'La cantidad de numeros'
        );

        if ($validator->fails()) {
            return $this->view->render('statistics', [
                'count'   => 0,
                'numbers' => [],
                'results' => null,
                'errors'  => $validator->errors(),
            ], 'Estadisticas');
        }

        $count = (int) trim($rawCount);

        return $this->view->render('statistics', [
            'count'   => $count,
            'numbers' => array_fill(0, $count, ''),
            'results' => null,
            'errors'  => [],
        ], 'Estadisticas');
    }

    /**
     * Segundo paso: analiza la serie y muestra las medidas estadisticas.
     */
    private function calculateStatistics(): string
    {
        $rawCount = (string) $this->request->postParam('count', '');
        $rawNumbers = $this->request->postParam('numbers', []);

        if (!is_array($rawNumbers)) {
            $rawNumbers = [];
        }

        $rawNumbers = array_values(array_map('strval', $rawNumbers));

        $validator = new Validator();
        $validator->required('count', $rawCount, 'La cantidad de numeros');
        $validator->integer('count', $rawCount, 'La cantidad de numeros');
        $validator->integerBetween(
            'count',
            $rawCount,
            self::MIN_COUNT,
            self::MAX_COUNT,
            'La cantidad de numeros'
        );

        if ($validator->passes() && count($rawNumbers) !== (int) trim($rawCount)) {
            $validator->error('numbers', sprintf(
                'Se esperaban %d numeros y se recibieron %d.',
                (int) trim($rawCount),
                count($rawNumbers)
            ));
        }

        if ($validator->passes()) {
            try {
                $numbers = $this->parser->parseList($rawNumbers);
            } catch (InvalidArgumentException $exception) {
                $validator->error('numbers', $exception->getMessage());
            }
        }

        if ($validator->fails()) {
            return $this->view->render('statistics', [
                'count'   => (int) $rawCount,
                'numbers' => $rawNumbers,
                'results' => null,
                'errors'  => $validator->errors(),
            ], 'Estadisticas');
        }

        $modes = $this->calculator->mode($numbers);

        return $this->view->render('statistics', [
            'count'   => count($rawNumbers),
            'numbers' => $rawNumbers,
            'results' => [
                'promedio' => $this->format($this->calculator->average($numbers)),
                'media'    => $this->format($this->calculator->median($numbers)),
                'moda'     => array_map($this->format(...), $modes),
                'sinModa'  => $modes === [],
            ],
            'errors'  => [],
        ], 'Estadisticas');
    }

    /**
     * Presenta un numero sin decimales sobrantes.
     */
    private function format(float $value): string
    {
        $formatted = rtrim(rtrim(number_format($value, self::PRECISION, '.', ''), '0'), '.');

        return $formatted === '' || $formatted === '-' ? '0' : $formatted;
    }
}
