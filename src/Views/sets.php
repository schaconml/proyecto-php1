<?php

declare(strict_types=1);

/**
 * Aplicacion 4: operaciones entre dos conjuntos de enteros.
 *
 * Variables esperadas:
 *  - $setA    (string) Texto del conjunto A.
 *  - $setB    (string) Texto del conjunto B.
 *  - $results (array<string, \App\Models\IntegerSet>) Resultados de las operaciones.
 *  - $errors  (array<string, string>) Errores de validacion indexados por campo.
 */
$titles = [
    'union'        => 'Unión (A ∪ B)',
    'intersection' => 'Intersección (A ∩ B)',
    'differenceAB' => 'Diferencia (A − B)',
    'differenceBA' => 'Diferencia (B − A)',
];
?>
<section class="app-card" aria-labelledby="sets-title">
    <h2 id="sets-title" class="app-title">4. Conjuntos</h2>

    <p class="app-lead">
        Ingrese dos conjuntos de números enteros separados por comas, espacios o saltos de línea.
        Los valores repetidos se descartan y cada resultado se muestra ordenado de forma ascendente.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/conjuntos')) ?>">
        <fieldset class="form-fieldset">
            <legend class="form-legend">Conjunto A</legend>

            <p class="form-field">
                <label class="form-label" for="setA">Elementos del conjunto A</label>
                <textarea class="form-input" id="setA" name="setA" rows="3"
                    placeholder="1, 2, 3, 4"><?= $this->e($setA) ?></textarea>
            </p>

            <?php if (isset($errors['setA'])): ?>
                <p class="form-error" role="alert"><?= $this->e($errors['setA']) ?></p>
            <?php endif; ?>
        </fieldset>

        <fieldset class="form-fieldset">
            <legend class="form-legend">Conjunto B</legend>

            <p class="form-field">
                <label class="form-label" for="setB">Elementos del conjunto B</label>
                <textarea class="form-input" id="setB" name="setB" rows="3"
                    placeholder="3, 4, 5, 6"><?= $this->e($setB) ?></textarea>
            </p>

            <?php if (isset($errors['setB'])): ?>
                <p class="form-error" role="alert"><?= $this->e($errors['setB']) ?></p>
            <?php endif; ?>
        </fieldset>

        <p class="form-actions">
            <button class="btn btn-primary" type="submit">Calcular</button>
        </p>
    </form>

    <?php if ($results !== []): ?>
        <section class="result-grid" aria-labelledby="sets-result-title" aria-live="polite">
            <h3 id="sets-result-title" class="result-title">Resultados</h3>

            <?php foreach ($results as $key => $set): ?>
                <article class="result-card">
                    <h4 class="result-name"><?= $this->e($titles[$key]) ?></h4>

                    <?php if ($set->isEmpty()): ?>
                        <p class="result-empty">El conjunto resultante está vacío.</p>
                    <?php else: ?>
                        <ul class="value-list">
                            <?php foreach ($set as $value): ?>
                                <li class="value-item"><?= $this->e($value) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <p class="result-note"><?= $this->e($set->size()) ?> elementos.</p>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <details class="app-details">
        <summary class="details-summary">Qué calcula cada operación</summary>
        <ul class="details-list">
            <li><strong>Unión:</strong> todos los elementos que están en A o en B (sin repetir).</li>
            <li><strong>Intersección:</strong> los elementos que están en A y en B a la vez.</li>
            <li><strong>Diferencia A − B:</strong> los elementos de A que no aparecen en B.</li>
            <li><strong>Diferencia B − A:</strong> los elementos de B que no aparecen en A.</li>
        </ul>
    </details>
</section>
