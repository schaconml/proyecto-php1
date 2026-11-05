<?php

declare(strict_types=1);

/**
 * Ejecutor de pruebas del proyecto.
 *
 * Uso:  C:\xampp\php\php.exe tests\run.php
 * Salida: 0 si todas las pruebas pasan, 1 si hay algun fallo.
 */

use App\Autoloader;
use Tests\Assert;

require_once dirname(__DIR__) . '/src/Autoloader.php';
require_once __DIR__ . '/Assert.php';

Autoloader::register();

$assert = new Assert();
$files = glob(__DIR__ . '/*Test.php') ?: [];

sort($files);

foreach ($files as $file) {
    $test = require $file;

    if (!is_callable($test)) {
        fwrite(STDERR, sprintf("El archivo %s no devuelve una prueba ejecutable.\n", basename($file)));
        exit(1);
    }

    $test($assert);
}

$failures = $assert->failures();

printf("Pruebas ejecutadas: %d\n", $assert->total());

if ($failures === []) {
    printf("RESULTADO: OK (%d aserciones)\n", $assert->total());
    exit(0);
}

printf("RESULTADO: %d fallo(s)\n", count($failures));

foreach ($failures as $failure) {
    printf("  - %s\n", $failure);
}

exit(1);
