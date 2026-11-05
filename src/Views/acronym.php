<?php

declare(strict_types=1);

/**
 * Aplicacion 1: conversor a acronimo.
 *
 * Variables esperadas:
 *  - $phrase  (string) Texto introducido por el usuario.
 *  - $acronym (string|null) Resultado de la conversion.
 *  - $errors  (array<string, string>) Errores de validacion indexados por campo.
 */
?>
<section class="app-card" aria-labelledby="acronym-title">
    <h2 id="acronym-title" class="app-title">1. Acronimo</h2>

    <p class="app-lead">
        Convierte una frase en su acronimo tomando la primera letra de cada palabra.
        Los guiones separan palabras como los espacios y el resto de los signos de
        puntuacion se eliminan.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/acronimo')) ?>">
        <p class="form-field">
            <label class="form-label" for="phrase">Frase o nombre completo</label>
            <textarea class="form-input" id="phrase" name="phrase" rows="4" maxlength="500"
                placeholder="Portable Network Graphics" required><?= $this->e($phrase) ?></textarea>
        </p>

        <?php if (isset($errors['phrase'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['phrase']) ?></p>
        <?php endif; ?>

        <p class="form-actions">
            <button class="btn btn-primary" type="submit">Convertir</button>
        </p>
    </form>

    <?php if ($acronym !== null && $acronym !== ''): ?>
        <section class="result-card" aria-labelledby="acronym-result-title" aria-live="polite">
            <h3 id="acronym-result-title" class="result-title">Acrónimo</h3>
            <p class="result-value">
                <output class="result-output" for="phrase"><?= $this->e($acronym) ?></output>
            </p>
        </section>
    <?php endif; ?>

    <details class="app-details">
        <summary class="details-summary">Ejemplos del enunciado</summary>
        <ul class="details-list">
            <li>As Soon As Possible &rarr; ASAP</li>
            <li>Liquid-crystal display &rarr; LCD</li>
            <li>Thank George It's Friday! &rarr; TGIF</li>
        </ul>
    </details>
</section>
