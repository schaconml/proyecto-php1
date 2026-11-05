<?php

declare(strict_types=1);

/**
 * Aplicacion 6: construccion de arboles binarios a partir de sus recorridos.
 *
 * Variables esperadas:
 *  - $preorder   (string) Texto del recorrido en preorden.
 *  - $inorder    (string) Texto del recorrido en inorden.
 *  - $postorder  (string) Texto del recorrido en postorden.
 *  - $layout     (array|null) Posiciones de nodos y aristas para el dibujo.
 *  - $traversals (array<string, list<string>>) Recorridos calculados sobre el arbol.
 *  - $errors     (array<string, string>) Errores de validacion indexados por campo.
 */
$labels = [
    'preorder'  => 'Preorden',
    'inorder'   => 'Inorden',
    'postorder' => 'Postorden',
];
?>
<section class="app-card" aria-labelledby="tree-title">
    <h2 id="tree-title" class="app-title">6. Árbol binario</h2>

    <p class="app-lead">
        Ingrese al menos dos de los tres recorridos (preorden, inorden y postorden) y la
        aplicación construirá el árbol. Si informa los tres, se utiliza el par
        <strong>preorden e inorden</strong>. Cada nodo se escribe con un solo carácter.
    </p>

    <form class="app-form" method="post" action="<?= $this->e($this->url('/arbol')) ?>">
        <?php foreach ($labels as $type => $label): ?>
            <fieldset class="form-fieldset">
                <legend class="form-legend"><?= $this->e($label) ?></legend>

                <p class="form-field">
                    <label class="form-label" for="<?= $this->e($type) ?>">Nodos en <?= $this->e(strtolower($label)) ?></label>
                    <input class="form-input" type="text" id="<?= $this->e($type) ?>" name="<?= $this->e($type) ?>"
                        value="<?= $this->e($type === 'preorder' ? $preorder : ($type === 'inorder' ? $inorder : $postorder)) ?>"
                        placeholder="A B D E C" autocomplete="off">
                </p>

                <?php if (isset($errors[$type])): ?>
                    <p class="form-error" role="alert"><?= $this->e($errors[$type]) ?></p>
                <?php endif; ?>
            </fieldset>
        <?php endforeach; ?>

        <?php if (isset($errors['tree'])): ?>
            <p class="form-error" role="alert"><?= $this->e($errors['tree']) ?></p>
        <?php endif; ?>

        <p class="form-actions">
            <button class="btn btn-primary" type="submit">Construir árbol</button>
        </p>
    </form>

    <?php if ($layout !== null): ?>
        <figure class="tree-figure">
            <svg class="tree-svg" viewBox="0 0 <?= $this->e($layout['width']) ?> <?= $this->e($layout['height']) ?>"
                role="img" aria-labelledby="tree-svg-title">
                <title id="tree-svg-title">Árbol binario construido a partir de los recorridos ingresados</title>

                <?php foreach ($layout['edges'] as $edge): ?>
                    <?php
                    $from = $layout['nodes'][$edge['from']];
                    $to = $layout['nodes'][$edge['to']];
                    ?>
                    <line class="tree-edge" x1="<?= $this->e($from['x']) ?>" y1="<?= $this->e($from['y']) ?>"
                        x2="<?= $this->e($to['x']) ?>" y2="<?= $this->e($to['y']) ?>"></line>
                <?php endforeach; ?>

                <?php foreach ($layout['nodes'] as $node): ?>
                    <g class="tree-node">
                        <circle class="tree-node-circle" cx="<?= $this->e($node['x']) ?>" cy="<?= $this->e($node['y']) ?>" r="24"></circle>
                        <text class="tree-node-label" x="<?= $this->e($node['x']) ?>" y="<?= $this->e($node['y']) ?>"><?= $this->e($node['value']) ?></text>
                    </g>
                <?php endforeach; ?>
            </svg>

            <figcaption class="tree-caption">
                Árbol reconstruido con <?= $this->e(count($layout['nodes'])) ?> nodos.
            </figcaption>
        </figure>

        <section class="result-card" aria-labelledby="tree-traversals-title">
            <h3 id="tree-traversals-title" class="result-title">Recorridos del árbol construido</h3>

            <dl class="result-list">
                <?php foreach ($labels as $type => $label): ?>
                    <?php if (isset($traversals[$type])): ?>
                        <dt><?= $this->e($label) ?></dt>
                        <dd><?= $this->e(implode(' ', $traversals[$type])) ?></dd>
                    <?php endif; ?>
                <?php endforeach; ?>
            </dl>
        </section>
    <?php endif; ?>

    <details class="app-details">
        <summary class="details-summary">Ejemplos para probar</summary>
        <ul class="details-list">
            <li>
                <strong>Ejemplo del enunciado (5 nodos):</strong>
                Preorden <code>A B D E C</code> &middot;
                Inorden <code>D B E A C</code> &middot;
                Postorden <code>D E B C A</code>
            </li>
            <li>
                <strong>Ejemplo de 11 nodos con números y letras:</strong>
                <a href="<?= $this->e($this->url('/arbol') . '?preorder=' . rawurlencode('1 2 4 8 9 5 A B 3 6 7') . '&amp;inorder=' . rawurlencode('8 4 9 2 A 5 B 1 6 3 7') . '&amp;postorder=' . rawurlencode('8 9 4 A B 5 2 6 7 3 1')) ?>">
                    Cargar este ejemplo en el formulario
                </a>
                <br>
                Preorden <code>1 2 4 8 9 5 A B 3 6 7</code> &middot;
                Inorden <code>8 4 9 2 A 5 B 1 6 3 7</code> &middot;
                Postorden <code>8 9 4 A B 5 2 6 7 3 1</code>
            </li>
            <li>Los nodos pueden ser <strong>letras o números</strong>, uno por carácter, sin repetidos.</li>
        </ul>
    </details>

    <details class="app-details">
        <summary class="details-summary">Cómo se reconstruye el árbol</summary>
        <ul class="details-list">
            <li><strong>Preorden e inorden:</strong> la raíz es el primer elemento del preorden y su posición en el inorden separa los subárboles.</li>
            <li><strong>Preorden y postorden:</strong> la raíz es el último elemento del postorden y la posición de la raíz del subárbol izquierdo define su tamaño.</li>
            <li><strong>Inorden y postorden:</strong> la raíz es el último elemento del postorden y su posición en el inorden separa los subárboles.</li>
            <li>La aplicación comprueba que los recorridos <strong>no se contradigan</strong> entre sí antes de dibujar el árbol.</li>
        </ul>
    </details>
</section>
