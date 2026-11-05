<?php

declare(strict_types=1);

use App\Models\CalculationOperation;

/**
 * Aplicacion 7: calculadora con historial almacenado en MySQL.
 *
 * Variables esperadas:
 *  - $number1   (string) Primer operando.
 *  - $number2   (string) Segundo operando.
 *  - $operation (string) Operacion seleccionada.
 *  - $result    (float|null) Resultado de la ultima operacion.
 *  - $history   (list<array>) Operaciones registradas.
 *  - $errors    (array<string, string>) Errores de validacion indexados por campo.
 *  - $notice    (string|null) Aviso de la ultima accion sobre el historial.
 */
$operations = [
    CalculationOperation::SUMA           => 'Suma',
    CalculationOperation::RESTA          => 'Resta',
    CalculationOperation::MULTIPLICACION => 'Multiplicación',
    CalculationOperation::DIVISION       => 'División',
    CalculationOperation::PORCENTAJE     => 'Porcentaje',
];
?>
<section class="app-card" aria-labelledby="calculator-title">
    <h2 id="calculator-title" class="app-title">7. Calculadora</h2>

    <p class="app-lead">
        Realice las operaciones básicas con números enteros o decimales. Cada operación queda
        registrada en el historial, que se puede borrar por completo o entrada por entrada.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/calculadora')) ?>">
        <p class="form-field">
            <label class="form-label" for="numero_1">Número 1</label>
            <input class="form-input" type="number" id="numero_1" name="numero_1" step="any"
                value="<?= $this->e($number1) ?>" autocomplete="off" required>
        </p>

        <?php if (isset($errors['numero_1'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['numero_1']) ?></p>
        <?php endif; ?>

        <p class="form-field">
            <label class="form-label" for="numero_2">Número 2</label>
            <input class="form-input" type="number" id="numero_2" name="numero_2" step="any"
                value="<?= $this->e($number2) ?>" autocomplete="off" required>
        </p>

        <?php if (isset($errors['numero_2'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['numero_2']) ?></p>
        <?php endif; ?>

        <p class="form-field">
            <label class="form-label" for="operacion">Operación</label>
            <select class="form-input" id="operacion" name="operacion">
                <?php foreach ($operations as $value => $label): ?>
                    <option value="<?= $this->e($value) ?>" <?= $this->e($operation === $value ? 'selected' : '') ?>>
                        <?= $this->e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php if (isset($errors['operacion'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['operacion']) ?></p>
        <?php endif; ?>

        <p class="form-actions">
            <button class="btn btn-primary" type="submit">Calcular</button>
        </p>
    </form>

    <?php if ($result !== null): ?>
        <section class="result-card" aria-labelledby="calculator-result-title" aria-live="polite">
            <h3 id="calculator-result-title" class="result-title">Resultado</h3>

            <p class="result-value">
                <output class="result-output" for="numero_1 numero_2"><?= $this->e($result) ?></output>
            </p>

            <p class="result-note">
                <?= $this->e($number1) ?> <?= $this->e($operations[$operation] ?? $operation) ?>
                <?= $this->e($number2) ?>
            </p>
        </section>
    <?php endif; ?>

    <?php if ($notice !== null): ?>
        <p class="form-notice" role="status"><?= $this->e($notice) ?></p>
    <?php endif; ?>

    <section class="history-card" aria-labelledby="history-title">
        <h3 id="history-title" class="result-title">Historial de operaciones</h3>

        <?php if ($history === []): ?>
            <p class="result-empty">Todavía no hay operaciones registradas.</p>
        <?php else: ?>
            <table class="history-table">
                <caption>Operaciones registradas en la base de datos</caption>
                <thead>
                    <tr>
                        <th scope="col">N.º</th>
                        <th scope="col">Número 1</th>
                        <th scope="col">Operación</th>
                        <th scope="col">Número 2</th>
                        <th scope="col">Resultado</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $entry): ?>
                        <tr>
                            <td><?= $this->e($entry['id']) ?></td>
                            <td><?= $this->e($entry['number1']) ?></td>
                            <td><?= $this->e($entry['operation']) ?></td>
                            <td><?= $this->e($entry['number2'] ?? '—') ?></td>
                            <td><?= $this->e($entry['result']) ?></td>
                            <td>
                                <time datetime="<?= $this->e($entry['isoDate']) ?>"><?= $this->e($entry['registeredAt']) ?></time>
                            </td>
                            <td>
                                <form method="post" action="<?= $this->e($this->url('/calculadora/historial/borrar/' . $entry['id'])) ?>">
                                    <button class="btn btn-small" type="submit">Borrar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form class="history-clear" method="post" action="<?= $this->e($this->url('/calculadora/historial/borrar')) ?>">
                <button class="btn btn-danger" type="submit">Borrar todo el historial</button>
            </form>
        <?php endif; ?>
    </section>

    <details class="app-details">
        <summary class="details-summary">Cómo se calcula cada operación</summary>
        <ul class="details-list">
            <li><strong>Suma, resta y multiplicación:</strong> se aplica la operación entre los dos números.</li>
            <li><strong>División:</strong> no se puede dividir entre cero; en ese caso la operación no se registra.</li>
            <li><strong>Porcentaje:</strong> el primer número es la base y el segundo el porcentaje (base 200 con 15 % da 30).</li>
        </ul>
    </details>
</section>
