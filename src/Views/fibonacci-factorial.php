<?php

declare(strict_types=1);

use App\Models\OperationSelector;

/**
 * Aplicacion 2: sucesion de Fibonacci o factorial.
 *
 * Variables esperadas:
 *  - $number    (string) Numero introducido por el usuario.
 *  - $operation (string) Operacion seleccionada (fibonacci o factorial).
 *  - $terms     (list<string>) Terminos de la sucesion de Fibonacci.
 *  - $factorial (string|null) Resultado del factorial.
 *  - $errors    (array<string, string>) Errores de validacion indexados por campo.
 */
$options = [
    OperationSelector::FIBONACCI => 'Sucesion de Fibonacci',
    OperationSelector::FACTORIAL => 'Factorial',
];
?>
<section class="app-card" aria-labelledby="fibonacci-title">
    <h2 id="fibonacci-title" class="app-title">2. Fibonacci y Factorial</h2>

    <p class="app-lead">
        Ingrese un numero y seleccione la operacion. La aplicacion genera la serie de
        Fibonacci o calcula el factorial segun la opcion elegida.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/fibonacci-factorial')) ?>">
        <fieldset class="form-fieldset">
            <legend class="form-legend">Operación</legend>

            <ul class="option-list">
                <?php foreach ($options as $value => $label): ?>
                    <li class="option-item">
                        <input class="option-input" type="radio" id="operation-<?= $this->e($value) ?>"
                            name="operation" value="<?= $this->e($value) ?>"
                            <?= $this->e($operation === $value ? 'checked' : '') ?>>
                        <label class="option-label" for="operation-<?= $this->e($value) ?>"><?= $this->e($label) ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </fieldset>

        <?php if (isset($errors['operation'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['operation']) ?></p>
        <?php endif; ?>

        <p class="form-field">
            <label class="form-label" for="number">Número</label>
            <input class="form-input" type="number" id="number" name="number" step="1"
                value="<?= $this->e($number) ?>" required>
        </p>

        <?php if (isset($errors['number'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['number']) ?></p>
        <?php endif; ?>

        <p class="form-actions">
            <button class="btn btn-primary" type="submit">Calcular</button>
        </p>
    </form>

    <?php if ($terms !== []): ?>
        <section class="result-card" aria-labelledby="fibonacci-result-title" aria-live="polite">
            <h3 id="fibonacci-result-title" class="result-title">Serie de Fibonacci</h3>

            <ol class="sequence">
                <?php foreach ($terms as $term): ?>
                    <li class="sequence-item"><?= $this->e($term) ?></li>
                <?php endforeach; ?>
            </ol>

            <p class="result-note"><?= $this->e(count($terms)) ?> términos generados.</p>
        </section>
    <?php endif; ?>

    <?php if ($factorial !== null): ?>
        <section class="result-card" aria-labelledby="factorial-result-title" aria-live="polite">
            <h3 id="factorial-result-title" class="result-title">Factorial</h3>

            <p class="result-value">
                <output class="result-output" for="number"><?= $this->e($factorial) ?></output>
            </p>

            <p class="result-note">
                <?= $this->e($number) ?>! = <?= $this->e($factorial) ?>
            </p>
        </section>
    <?php endif; ?>

    <details class="app-details">
        <summary class="details-summary">Cómo se calcula</summary>
        <ul class="details-list">
            <li><strong>Fibonacci:</strong> la serie empieza en 0 y 1, y cada término es la suma de los dos anteriores.</li>
            <li><strong>Factorial:</strong> es el producto de todos los enteros desde 1 hasta el número ingresado (0! = 1).</li>
            <li>A partir de 21! el resultado excede el rango de un entero de 64 bits y se calcula con aritmética de precisión arbitraria.</li>
        </ul>
    </details>
</section>
