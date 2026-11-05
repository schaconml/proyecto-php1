<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CalculationOperation;
use App\Models\Calculator;
use App\Models\CalculatorHistoryEntry;
use App\Models\CalculatorHistoryRepository;
use App\Models\Validator;
use App\Views\View;
use DivisionByZeroError;
use PDOException;

/**
 * Controlador de la calculadora con historial.
 *
 * Responsabilidad unica: leer los operandos, delegar el calculo en el modelo
 * y pedir al repositorio que registre o borre el historial. No contiene
 * consultas SQL ni algoritmos matematicos.
 */
final class CalculatorController
{
    /**
     * Limite de los valores, acorde con la columna DECIMAL(10,4).
     */
    private const MAX_VALUE = 999999.9999;

    public function __construct(
        private readonly Request $request,
        private readonly View $view,
        private readonly Calculator $calculator,
        private readonly CalculatorHistoryRepository $repository
    ) {
    }

    /**
     * Muestra la calculadora y el historial.
     */
    public function index(): string
    {
        return $this->render();
    }

    /**
     * Calcula la operacion y la registra en el historial.
     */
    public function store(): string
    {
        $rawFirst = (string) $this->request->postParam('numero_1', '');
        $rawSecond = (string) $this->request->postParam('numero_2', '');
        $operation = (string) $this->request->postParam('operacion', CalculationOperation::SUMA);

        $errors = [];
        $result = null;

        $validator = new Validator();
        $validator->required('numero_1', $rawFirst, 'El primer numero');
        $validator->decimal('numero_1', $rawFirst, 'El primer numero');
        $validator->decimalBetween('numero_1', $rawFirst, -self::MAX_VALUE, self::MAX_VALUE, 'El primer numero');
        $validator->required('numero_2', $rawSecond, 'El segundo numero');
        $validator->decimal('numero_2', $rawSecond, 'El segundo numero');
        $validator->decimalBetween('numero_2', $rawSecond, -self::MAX_VALUE, self::MAX_VALUE, 'El segundo numero');
        $validator->required('operacion', $operation, 'La operacion');

        if (!CalculationOperation::isValid($operation)) {
            $validator->error('operacion', 'La operacion seleccionada no es valida.');
        }

        if ($validator->passes()) {
            $first = (float) str_replace(',', '.', trim($rawFirst));
            $second = (float) str_replace(',', '.', trim($rawSecond));

            try {
                $result = $this->calculator->calculate($first, $operation, $second);
                $this->repository->save($first, $operation, $second, $result);
            } catch (DivisionByZeroError $exception) {
                $validator->error('numero_2', $exception->getMessage());
                $result = null;
            } catch (PDOException $exception) {
                $validator->error('operacion', 'No fue posible registrar la operacion en el historial.');
                $result = null;
            }
        }

        if ($validator->fails()) {
            $errors = $validator->errors();
        }

        return $this->render([
            'number1'  => $rawFirst,
            'number2'  => $rawSecond,
            'operation' => $operation,
            'result'   => $result,
            'errors'   => $errors,
        ]);
    }

    /**
     * Borra todo el historial.
     */
    public function clearHistory(): string
    {
        $this->repository->deleteAll();

        return $this->render([
            'notice' => 'El historial fue borrado por completo.',
        ]);
    }

    /**
     * Borra una operacion del historial.
     */
    public function deleteEntry(string $id): string
    {
        if (ctype_digit($id)) {
            $this->repository->deleteById((int) $id);
        }

        return $this->render([
            'notice' => 'La operacion fue borrada del historial.',
        ]);
    }

    /**
     * Prepara los datos y renderiza la vista de la calculadora.
     *
     * @param array<string, mixed> $data
     */
    private function render(array $data = []): string
    {
        $history = array_map(
            static fn (CalculatorHistoryEntry $entry): array => $entry->toRow(),
            $this->repository->findAll()
        );

        return $this->view->render('calculator', array_merge([
            'number1'   => '',
            'number2'   => '',
            'operation' => CalculationOperation::SUMA,
            'result'    => null,
            'history'   => $history,
            'errors'    => [],
            'notice'    => null,
        ], $data), 'Calculadora');
    }
}
