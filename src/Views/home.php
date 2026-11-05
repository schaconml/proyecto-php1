<?php

declare(strict_types=1);

/**
 * Pagina principal: menu con las 7 aplicaciones del taller.
 *
 * Variables esperadas: $modules (lista de arrays con number, name y description).
 */
?>
<section class="app-card" aria-labelledby="home-title">
    <h2 id="home-title" class="app-title">Menu de aplicaciones</h2>

    <p class="app-lead">
        Cada modulo del taller se desarrolla como una aplicacion independiente sobre una
        arquitectura MVC en PHP Orientado a Objetos, sin JavaScript y con estilos externos.
    </p>

    <ul class="module-grid">
        <?php foreach ($modules as $module): ?>
            <li class="module-item">
                <article class="module-card">
                    <h3 class="module-title"><?= $this->e($module['number']) ?>. <?= $this->e($module['name']) ?></h3>
                    <p class="module-description"><?= $this->e($module['description']) ?></p>

                    <?php if ($module['ready'] && $module['slug'] !== null): ?>
                        <p class="form-actions">
                            <a class="btn btn-primary" href="<?= $this->e($this->url('/' . $module['slug'])) ?>">Abrir aplicacion</a>
                        </p>
                        <p class="module-badge module-badge-ready">Disponible</p>
                    <?php else: ?>
                        <p class="module-badge">Pendiente de desarrollo</p>
                    <?php endif; ?>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
