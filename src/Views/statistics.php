<?php

declare(strict_types=1);

/**
 * Aplicacion 3: promedio, media (mediana) y moda de una serie de numeros.
 *
 * Variables esperadas:
 *  - $count   (int) Cantidad de numeros que va a ingresar el usuario.
 *  - $numbers (list<string>) Valores digitados por el usuario.
 *  - $results (array|null) Medidas calculadas.
 *  - $errors  (array<string, string>) Errores de validacion indexados por campo.
 */
?>
<section class="app-card" aria-labelledby="statistics-title">
    <h2 id="statistics-title" class="app-title">3. Estadísticas</h2>

    <p class="app-lead">
        Calcule el <strong>promedio</strong> (media aritmética), la <strong>media</strong>
        (mediana) y la <strong>moda</strong> de una serie de números reales. Usted decide
        cuántos números va a ingresar: la aplicación genera los campos correspondientes.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/estadisticas')) ?>">
        <fieldset class="form-fieldset">
            <legend class="form-legend">Cantidad de números</legend>

            <p class="form-field">
                <label class="form-label" for="count">Número de valores de la serie</label>
                <input class="form-input form-input-compact" type="number" id="count" name="count"
                    min="1" max="100" step="1" value="<?= $this->e($count > 0 ? $count : 5) ?>" required>
            </p>

            <?php if (isset($errors['count'])): ?>
                <p class="form-error" role="alert"><?= $this->e($errors['count']) ?></p>
            <?php endif; ?>

            <p class="form-actions">
                <button class="btn" type="submit" name="step" value="amount">Generar campos</button>
            </p>
        </fieldset>

        <?php if ($count > 0): ?>
            <fieldset class="form-fieldset">
                <legend class="form-legend">Serie de números</legend>

                <ul class="number-list">
                    <?php foreach ($numbers as $position => $value): ?>
                        <li class="number-item">
                            <label class="number-label" for="number-<?= $this->e($position + 1) ?>">
                                N.º <?= $this->e($position + 1) ?>
                            </label>
                            <input class="form-input" type="number" id="number-<?= $this->e($position + 1) ?>"
                                name="numbers[]" step="any" value="<?= $this->e($value) ?>"
                                autocomplete="off">
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if (isset($errors['numbers'])): ?>
                    <p class="form-error" role="alert"><?= $this->e($errors['numbers']) ?></p>
                <?php endif; ?>

                <p class="form-actions">
                    <button class="btn btn-primary" type="submit" name="step" value="statistics">
                        Calcular estadísticas
                    </button>
                </p>
            </fieldset>
        <?php endif; ?>
    </form>

    <?php if ($results !== null): ?>
        <section class="result-card" aria-labelledby="statistics-result-title" aria-live="polite">
            <h3 id="statistics-result-title" class="result-title">Medidas de la serie</h3>

            <dl class="result-list">
                <dt>Promedio (media aritmética)</dt>
                <dd><?= $this->e($results['promedio']) ?></dd>

                <dt>Media (mediana)</dt>
                <dd><?= $this->e($results['media']) ?></dd>

                <dt>Moda</dt>
                <dd>
                    <?php if ($results['sinModa']): ?>
                        No hay moda (todos los valores son distintos)
                    <?php else: ?>
                        <?= $this->e(implode(', ', $results['moda'])) ?>
                    <?php endif; ?>
                </dd>
            </dl>

            <p class="result-note">
                Serie analizada: <?= $this->e($count) ?> <?= $this->e($count === 1 ? 'valor' : 'valores') ?>.
            </p>
        </section>
    <?php endif; ?>

    <details class="app-details">
        <summary class="details-summary">Qué calcula cada medida</summary>
        <ul class="details-list">
            <li><strong>Promedio:</strong> suma de todos los valores dividida entre la cantidad de valores.</li>
            <li><strong>Media (mediana):</strong> valor central de la serie ordenada; si la cantidad es par, es el promedio de los dos valores centrales.</li>
            <li><strong>Moda:</strong> valor o valores que más veces se repiten; si ningún valor se repite, no hay moda.</li>
        </ul>
    </details>
</section>
