<?php

declare(strict_types=1);

/**
 * Verificacion automatica de las reglas del proyecto sobre el codigo fuente.
 *
 * Uso:  C:\xampp\php\php.exe tests\rules-check.php
 * Salida: 0 si no se encuentra ninguna infraccion, 1 en caso contrario.
 *
 * Reglas comprobadas (SKILLS/rule_01, rule_02 y prohibicion de JavaScript):
 *  - Sin <div>, <span>, <b>, <i> ni etiquetas pre-HTML5.
 *  - Sin estilos inline (style="...") ni bloques <style>.
 *  - Sin JavaScript: ni <script>, ni atributos on*, ni archivos .js.
 */

$root = dirname(__DIR__);
$violations = [];

$forbiddenTags = ['<div', '<span', '<b>', '<b ', '<i>', '<i ', '<font', '<center', '<strike', '<marquee'];
$forbiddenAttributes = ['style=', 'onclick=', 'onload=', 'onchange=', 'oninput=', 'onsubmit=', 'javascript:'];

/**
 * Elimina los comentarios del codigo para no producir falsos positivos
 * (por ejemplo, cuando la documentacion menciona las etiquetas prohibidas).
 */
function stripComments(string $content, string $extension): string
{
    if ($extension === 'html') {
        return (string) preg_replace('/<!--.*?-->/s', '', $content);
    }

    if ($extension !== 'php') {
        return $content;
    }

    $clean = '';

    foreach (token_get_all($content) as $token) {
        if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
            continue;
        }

        $clean .= is_array($token) ? $token[1] : $token;
    }

    return $clean;
}

/**
 * Recorre los archivos de una carpeta de forma recursiva.
 *
 * @return list<string>
 */
function collectFiles(string $directory, array $extensions): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        if (in_array(strtolower($file->getExtension()), $extensions, true)) {
            $files[] = $file->getPathname();
        }
    }

    sort($files);

    return $files;
}

// 1) Plantillas y front controller: etiquetas y atributos prohibidos.
$targets = array_merge(
    collectFiles($root . '/src/Views', ['php', 'html']),
    collectFiles($root . '/public', ['php', 'html'])
);

foreach ($targets as $file) {
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $content = stripComments((string) file_get_contents($file), $extension);
    $relative = str_replace('\\', '/', substr($file, strlen($root) + 1));

    foreach ($forbiddenTags as $tag) {
        if (stripos($content, $tag) !== false) {
            $violations[] = sprintf('%s: etiqueta prohibida "%s"', $relative, $tag);
        }
    }

    if (stripos($content, '<style') !== false) {
        $violations[] = sprintf('%s: bloque <style> prohibido', $relative);
    }

    if (stripos($content, '<script') !== false) {
        $violations[] = sprintf('%s: etiqueta <script> prohibida (JavaScript)', $relative);
    }

    foreach ($forbiddenAttributes as $attribute) {
        if (stripos($content, $attribute) !== false) {
            $violations[] = sprintf('%s: atributo prohibido "%s"', $relative, $attribute);
        }
    }
}

// 2) No debe existir ningun archivo JavaScript en el proyecto.
$jsFiles = collectFiles($root, ['js', 'mjs', 'cjs']);

foreach ($jsFiles as $file) {
    $relative = str_replace('\\', '/', substr($file, strlen($root) + 1));

    if (str_contains($relative, '/.git/') || str_starts_with($relative, '.git/')) {
        continue;
    }

    $violations[] = sprintf('%s: archivo JavaScript prohibido', $relative);
}

// 3) Los estilos deben vivir solo en /public/css.
$cssFiles = collectFiles($root . '/public', ['css']);

if ($cssFiles === []) {
    $violations[] = 'public/css: no se encontro ninguna hoja de estilos externa';
}

$cssOutside = collectFiles($root . '/src', ['css']);

foreach ($cssOutside as $file) {
    $violations[] = sprintf(
        '%s: el CSS debe resideir unicamente en /public/css',
        str_replace('\\', '/', substr($file, strlen($root) + 1))
    );
}

printf("Archivos revisados: %d\n", count($targets));

if ($violations === []) {
    printf("REGLAS: OK (%d Plantillas verificadas, %d hojas de estilo)\n", count($targets), count($cssFiles));
    exit(0);
}

printf("REGLAS: %d infraccion(es)\n", count($violations));

foreach ($violations as $violation) {
    printf("  - %s\n", $violation);
}

exit(1);
