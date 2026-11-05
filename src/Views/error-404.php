<?php

declare(strict_types=1);

/**
 * Pagina de error 404.
 */
?>
<section class="app-card" aria-labelledby="error-title">
    <h2 id="error-title" class="app-title">404 - Pagina no encontrada</h2>

    <p class="app-lead">
        La ruta solicitada no corresponde a ninguna aplicacion del taller.
    </p>

    <p class="app-action">
        <a class="nav-link" href="<?= $this->e($this->url('/')) ?>">Volver al menu principal</a>
    </p>
</section>
