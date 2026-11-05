<?php

declare(strict_types=1);

/**
 * Pagina de error inesperada (500).
 *
 * Variables esperadas:
 *  - $message (string) Descripcion del problema, apta para mostrar al usuario.
 */
?>
<section class="app-card" aria-labelledby="error-title">
    <h2 id="error-title" class="app-title">Ocurrió un error inesperado</h2>

    <p class="app-lead"><?= $this->e($message) ?></p>

    <p class="app-action">
        <a class="nav-link" href="<?= $this->e($this->url('/')) ?>">Volver al menu principal</a>
    </p>
</section>
