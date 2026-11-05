<?php

declare(strict_types=1);

/**
 * Aplicacion 5: conversor de entero a binario.
 *
 * Variables esperadas:
 *  - $number  (string) Numero introducido por el usuario.
 *  - $binary  (string|null) Resultado de la conversion.
 *  - $decimal (int|null) Valor decimal de referencia.
 *  - $bits    (int|null) Cantidad de bits del resultado.
 *  - $errors  (array<string, string>) Errores de validacion indexados por campo.
 */
?>
<section class="app-card" aria-labelledby="binary-title">
    <h2 id="binary-title" class="app-title">5. Conversor a binario</h2>

    <p class="app-lead">
        Convierte un numero entero en su representacion binaria usando divisiones
        sucesivas entre 2. Admite valores positivos y negativos.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/binario')) ?>">
        <p class="form-field">
            <label class="form-label" for="number">Numero entero</label>
            <input class="form-input" type="number" id="number" name="number" step="1"
                value="<?= $this->e($number) ?>" required>
        </p>

        <?php if (isset($errors['number'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['number']) ?></p>
        <?php endif; ?>

        <p class="form-actions">
            <button class="btn btn-primary" type="submit">Convertir</button>
        </p>
    </form>

    <?php if ($binary !== null): ?>
        <section class="result-card" aria-labelledby="binary-result-title" aria-live="polite">
            <h3 id="binary-result-title" class="result-title">Resultado</h3>

            <p class="result-value">
                <output class="result-output" for="number"><?= $this->e($binary) ?></output>
            </p>

            <dl class="result-list">
                <dt>Decimal</dt>
                <dd><?= $this->e($decimal) ?></dd>

                <dt>Binario</dt>
                <dd><code><?= $this->e($binary) ?></code></dd>

                <dt>Bits</dt>
                <dd><?= $this->e($bits) ?></dd>
            </dl>
        </section>
    <?php endif; ?>

    <details class="app-details">
        <summary class="details-summary">Como funciona el algoritmo</summary>
        <ul class="details-list">
            <li>Se divide el numero entre 2 y se toma el resto de cada division, que siempre es 0 o 1.</li>
            <li>Los restos se leen de abajo hacia arriba para reconstruir el numero binario.</li>
            <li>El cero se representa como "0" y los negativos llevan el signo delante (signo y magnitud).</li>
        </ul>
    </details>
</section>
