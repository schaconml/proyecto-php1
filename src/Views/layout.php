<?php

declare(strict_types=1);

/**
 * Layout principal de la aplicacion.
 *
 * Variables esperadas: $title (string), $content (string HTML ya renderizado).
 * Estructura semantica HTML5: header / nav / main / footer (sin <div> ni <span>).
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($title) ?> | Taller PHP</title>
    <link rel="stylesheet" href="<?= $this->e($this->asset('css/base.css')) ?>">
    <link rel="stylesheet" href="<?= $this->e($this->asset('css/modules.css')) ?>">
</head>
<body>
    <header class="site-header">
        <h1 class="site-title">Taller PHP</h1>

        <nav class="site-nav" aria-label="Navegacion principal">
            <ul class="nav-list">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->e($this->url('/')) ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->e($this->url('/acronimo')) ?>">Acronimo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->e($this->url('/fibonacci-factorial')) ?>">Fibonacci / Factorial</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->e($this->url('/conjuntos')) ?>">Conjuntos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->e($this->url('/binario')) ?>">Binario</a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="site-main">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <p class="footer-text">
            Taller PHP &middot; 7 aplicaciones matematicas y logicas en PHP Orientado a Objetos.
        </p>
    </footer>
</body>
</html>
